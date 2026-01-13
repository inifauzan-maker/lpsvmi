<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Student;
use App\Models\TargetMarket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller
{
    public function index()
    {
        $students = Student::query()->orderBy('id')->get();
        $leads = Lead::query()->orderBy('id')->get();
        $targets = TargetMarket::query()->orderBy('id')->get();

        $data = [];
        foreach ($students as $student) {
            $data[] = $this->formatStudent($student);
        }
        foreach ($leads as $lead) {
            $data[] = $this->formatLead($lead);
        }
        foreach ($targets as $target) {
            $data[] = $this->formatTargetMarket($target);
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $module = $request->input('module');
        if (!in_array($module, ['data-siswa', 'data-leads', 'target-market'], true)) {
            return response()->json(['message' => 'Modul tidak valid.'], 422);
        }

        $payload = $request->except(['module', '__backendId']);
        if (!isset($payload['createdAt'])) {
            $payload['createdAt'] = now()->toIso8601String();
        }

        if ($module === 'target-market') {
            $duplicate = $this->findTargetMarketDuplicate($payload);
            if ($duplicate) {
                return response()->json(['message' => 'Target sudah ada.'], 409);
            }
        }

        $record = $this->createRecord($module, $payload);

        return response()->json([
            'isOk' => true,
            'data' => $this->formatResponse($module, $record),
        ]);
    }

    public function update(Request $request, string $backendId)
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        [$module, $id] = $this->parseBackendId($backendId, $request->input('module'));
        if (!$module || !$id) {
            return response()->json(['message' => 'ID tidak valid.'], 422);
        }

        $record = $this->findRecord($module, $id);
        if (!$record) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $updates = $request->except(['module', '__backendId']);
        $this->applyUpdates($module, $record, $updates);

        return response()->json([
            'isOk' => true,
            'data' => $this->formatResponse($module, $record),
        ]);
    }

    public function destroy(Request $request, string $backendId)
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        [$module, $id] = $this->parseBackendId($backendId, $request->input('module'));
        if (!$module || !$id) {
            return response()->json(['message' => 'ID tidak valid.'], 422);
        }

        $record = $this->findRecord($module, $id);

        if (!$record) {
            return response()->json(['isOk' => true]);
        }

        $record->delete();

        return response()->json(['isOk' => true]);
    }

    private function formatResponse(string $module, $record): array
    {
        if ($module === 'data-siswa') {
            return $this->formatStudent($record);
        }

        if ($module === 'data-leads') {
            return $this->formatLead($record);
        }

        return $this->formatTargetMarket($record);
    }

    private function parseBackendId(string $backendId, ?string $fallbackModule = null): array
    {
        if (str_contains($backendId, ':')) {
            [$module, $id] = explode(':', $backendId, 2);
            return [$module, (int) $id];
        }

        if ($fallbackModule) {
            return [$fallbackModule, (int) $backendId];
        }

        return [null, null];
    }

    private function findTargetMarketDuplicate(array $payload): bool
    {
        $required = ['wilayah', 'program', 'bulan', 'tahun'];
        foreach ($required as $field) {
            if (!isset($payload[$field])) {
                return false;
            }
        }

        return TargetMarket::query()
            ->where('wilayah', $payload['wilayah'])
            ->where('program', $payload['program'])
            ->where('bulan', (int) $payload['bulan'])
            ->where('tahun', (int) $payload['tahun'])
            ->exists();
    }

    private function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array($user->role, ['admin', 'super admin'], true);
    }

    private function createRecord(string $module, array $payload)
    {
        if ($module === 'data-siswa') {
            return Student::query()->create($this->mapStudentPayload($payload));
        }

        if ($module === 'data-leads') {
            return Lead::query()->create($this->mapLeadPayload($payload));
        }

        return TargetMarket::query()->create($this->mapTargetMarketPayload($payload));
    }

    private function findRecord(string $module, int $id)
    {
        if ($module === 'data-siswa') {
            return Student::query()->find($id);
        }

        if ($module === 'data-leads') {
            return Lead::query()->find($id);
        }

        return TargetMarket::query()->find($id);
    }

    private function applyUpdates(string $module, $record, array $updates): void
    {
        if ($module === 'data-siswa') {
            $record->fill($this->mapStudentPayload($updates, true));
        } elseif ($module === 'data-leads') {
            $record->fill($this->mapLeadPayload($updates, true));
        } else {
            $record->fill($this->mapTargetMarketPayload($updates, true));
        }
        $record->save();
    }

    private function mapStudentPayload(array $payload, bool $partial = false): array
    {
        $mapped = [];
        $this->assignIf($mapped, 'nama', $payload, 'nama', $partial);
        $this->assignIf($mapped, 'ttl', $payload, 'ttl', $partial);
        $this->assignIf($mapped, 'no_hp_siswa', $payload, 'no_hp_siswa', $partial);
        $this->assignIf($mapped, 'asal_sekolah', $payload, 'asal_sekolah', $partial);
        $this->assignIf($mapped, 'kelas', $payload, 'kelas', $partial);
        $this->assignIf($mapped, 'asal_kota', $payload, 'asal_kota', $partial);
        $this->assignIf($mapped, 'program', $payload, 'program', $partial);
        $this->assignIf($mapped, 'nama_medsos', $payload, 'nama_medsos', $partial);
        $this->assignIf($mapped, 'alamat_siswa', $payload, 'alamat_siswa', $partial);
        $this->assignIf($mapped, 'nama_ortu', $payload, 'nama_ortu', $partial);
        $this->assignIf($mapped, 'no_tlp_ortu', $payload, 'no_tlp_ortu', $partial);
        $this->assignIf($mapped, 'location', $payload, 'location', $partial);

        if ($this->hasKey($payload, 'fromLeadsId', $partial)) {
            $mapped['from_leads_id'] = $payload['fromLeadsId'] ?? null;
        }

        if ($this->hasKey($payload, 'platform_medsos', $partial)) {
            $mapped['platform_medsos'] = $this->normalizeArray($payload['platform_medsos'] ?? null);
        }

        if ($this->hasKey($payload, 'info_villa_merah_dari', $partial)) {
            $mapped['info_villa_merah_dari'] = $this->normalizeArray($payload['info_villa_merah_dari'] ?? null);
        }

        if ($this->hasKey($payload, 'biaya_pendidikan', $partial)) {
            $mapped['biaya_pendidikan'] = $this->toInt($payload['biaya_pendidikan'] ?? null);
        }

        if ($this->hasKey($payload, 'sisa_angsuran', $partial)) {
            $mapped['sisa_angsuran'] = $this->toInt($payload['sisa_angsuran'] ?? null);
        }

        if ($this->hasKey($payload, 'createdAt', $partial) || $this->hasKey($payload, 'tanggal_daftar', $partial)) {
            $value = $payload['tanggal_daftar'] ?? $payload['createdAt'] ?? null;
            $mapped['tanggal_daftar'] = $this->parseDate($value);
        }

        return $mapped;
    }

    private function mapLeadPayload(array $payload, bool $partial = false): array
    {
        $mapped = [];
        $this->assignIf($mapped, 'nama_siswa', $payload, 'nama_siswa', $partial);
        $this->assignIf($mapped, 'ttl', $payload, 'ttl', $partial);
        $this->assignIf($mapped, 'program', $payload, 'program', $partial);
        $this->assignIf($mapped, 'asal_sekolah', $payload, 'asal_sekolah', $partial);
        $this->assignIf($mapped, 'kelas', $payload, 'kelas', $partial);
        $this->assignIf($mapped, 'asal_kota', $payload, 'asal_kota', $partial);
        $this->assignIf($mapped, 'no_hp', $payload, 'no_hp', $partial);
        $this->assignIf($mapped, 'nama_medsos', $payload, 'nama_medsos', $partial);
        $this->assignIf($mapped, 'nama_ortu', $payload, 'nama_ortu', $partial);
        $this->assignIf($mapped, 'no_tlp_ortu', $payload, 'no_tlp_ortu', $partial);
        $this->assignIf($mapped, 'alamat', $payload, 'alamat', $partial);
        $this->assignIf($mapped, 'wilayah', $payload, 'wilayah', $partial);
        $this->assignIf($mapped, 'status_crm', $payload, 'status_crm', $partial);
        $this->assignIf($mapped, 'catatan_crm', $payload, 'catatan_crm', $partial);
        $this->assignIf($mapped, 'program_pendidikan', $payload, 'program_pendidikan', $partial);

        if ($this->hasKey($payload, 'platform_medsos', $partial)) {
            $mapped['platform_medsos'] = $this->normalizeArray($payload['platform_medsos'] ?? null);
        }

        if ($this->hasKey($payload, 'info_villa_merah_dari', $partial)) {
            $mapped['info_villa_merah_dari'] = $this->normalizeArray($payload['info_villa_merah_dari'] ?? null);
        }

        if ($this->hasKey($payload, 'is_closing', $partial)) {
            $mapped['is_closing'] = filter_var(
                $payload['is_closing'] ?? false,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false;
        }

        if ($this->hasKey($payload, 'tgl_followup', $partial)) {
            $mapped['tgl_followup'] = $this->parseDate($payload['tgl_followup'] ?? null);
        }

        if ($this->hasKey($payload, 'tanggal_closing', $partial)) {
            $mapped['tanggal_closing'] = $this->parseDate($payload['tanggal_closing'] ?? null);
        }

        if ($this->hasKey($payload, 'tanggal_input', $partial) || $this->hasKey($payload, 'createdAt', $partial)) {
            $value = $payload['tanggal_input'] ?? $payload['createdAt'] ?? null;
            $mapped['tanggal_input'] = $this->parseDate($value);
        }

        return $mapped;
    }

    private function mapTargetMarketPayload(array $payload, bool $partial = false): array
    {
        $mapped = [];
        $this->assignIf($mapped, 'wilayah', $payload, 'wilayah', $partial);
        $this->assignIf($mapped, 'program', $payload, 'program', $partial);

        if ($this->hasKey($payload, 'bulan', $partial)) {
            $mapped['bulan'] = $this->toInt($payload['bulan'] ?? null);
        }

        if ($this->hasKey($payload, 'tahun', $partial)) {
            $mapped['tahun'] = $this->toInt($payload['tahun'] ?? null);
        }

        if ($this->hasKey($payload, 'target_siswa', $partial)) {
            $mapped['target_siswa'] = $this->toInt($payload['target_siswa'] ?? null);
        }

        if ($this->hasKey($payload, 'target_omset', $partial)) {
            $mapped['target_omset'] = $this->toInt($payload['target_omset'] ?? null);
        }

        return $mapped;
    }

    private function formatStudent(Student $student): array
    {
        return [
            '__backendId' => 'data-siswa:' . $student->id,
            'module' => 'data-siswa',
            'nama' => $student->nama,
            'ttl' => $student->ttl,
            'no_hp_siswa' => $student->no_hp_siswa,
            'asal_sekolah' => $student->asal_sekolah,
            'kelas' => $student->kelas,
            'asal_kota' => $student->asal_kota,
            'program' => $student->program,
            'nama_medsos' => $student->nama_medsos,
            'platform_medsos' => $student->platform_medsos ?? [],
            'info_villa_merah_dari' => $student->info_villa_merah_dari ?? [],
            'alamat_siswa' => $student->alamat_siswa,
            'nama_ortu' => $student->nama_ortu,
            'no_tlp_ortu' => $student->no_tlp_ortu,
            'biaya_pendidikan' => $student->biaya_pendidikan,
            'sisa_angsuran' => $student->sisa_angsuran,
            'location' => $student->location,
            'fromLeadsId' => $student->from_leads_id,
            'createdAt' => $this->formatDate($student->tanggal_daftar, $student->created_at),
        ];
    }

    private function formatLead(Lead $lead): array
    {
        return [
            '__backendId' => 'data-leads:' . $lead->id,
            'module' => 'data-leads',
            'nama_siswa' => $lead->nama_siswa,
            'ttl' => $lead->ttl,
            'program' => $lead->program,
            'asal_sekolah' => $lead->asal_sekolah,
            'kelas' => $lead->kelas,
            'asal_kota' => $lead->asal_kota,
            'no_hp' => $lead->no_hp,
            'nama_medsos' => $lead->nama_medsos,
            'nama_ortu' => $lead->nama_ortu,
            'no_tlp_ortu' => $lead->no_tlp_ortu,
            'tanggal_input' => $this->formatDate($lead->tanggal_input),
            'alamat' => $lead->alamat,
            'wilayah' => $lead->wilayah,
            'platform_medsos' => $lead->platform_medsos ?? [],
            'info_villa_merah_dari' => $lead->info_villa_merah_dari ?? [],
            'tgl_followup' => $this->formatDate($lead->tgl_followup),
            'status_crm' => $lead->status_crm,
            'tanggal_closing' => $this->formatDate($lead->tanggal_closing),
            'catatan_crm' => $lead->catatan_crm,
            'program_pendidikan' => $lead->program_pendidikan,
            'is_closing' => (bool) $lead->is_closing,
            'createdAt' => $this->formatDate($lead->tanggal_input, $lead->created_at),
        ];
    }

    private function formatTargetMarket(TargetMarket $target): array
    {
        return [
            '__backendId' => 'target-market:' . $target->id,
            'module' => 'target-market',
            'wilayah' => $target->wilayah,
            'program' => $target->program,
            'bulan' => $target->bulan,
            'tahun' => $target->tahun,
            'target_siswa' => $target->target_siswa,
            'target_omset' => $target->target_omset,
        ];
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatDate($value, $fallback = null): ?string
    {
        if ($value) {
            return Carbon::parse($value)->toDateString();
        }
        if ($fallback) {
            return Carbon::parse($fallback)->toDateString();
        }
        return null;
    }

    private function normalizeArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $parts = array_filter(array_map('trim', explode(',', $value)));
            return array_values($parts);
        }
        return [];
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        $normalized = preg_replace('/[^0-9-]/', '', (string) $value);
        return $normalized === '' ? null : (int) $normalized;
    }

    private function hasKey(array $payload, string $key, bool $partial): bool
    {
        return !$partial || array_key_exists($key, $payload);
    }

    private function assignIf(array &$mapped, string $field, array $payload, string $key, bool $partial): void
    {
        if ($this->hasKey($payload, $key, $partial)) {
            $mapped[$field] = $payload[$key] ?? null;
        }
    }
}
