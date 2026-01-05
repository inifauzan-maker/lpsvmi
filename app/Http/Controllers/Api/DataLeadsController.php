<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLead;
use Illuminate\Http\Request;

class DataLeadsController extends Controller
{
    public function index(Request $request)
    {
        $query = DataLead::query()->orderByDesc('id');
        $dateField = $this->resolveDateField($request, ['tanggal_input', 'created_at'], 'created_at');

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->query('wilayah'));
        }
        if ($request->filled('program')) {
            $query->where('program', $request->query('program'));
        }
        if ($request->filled('status_crm')) {
            $query->where('status_crm', $request->query('status_crm'));
        }
        if ($request->filled('bulan')) {
            $query->whereMonth($dateField, (int) $request->query('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear($dateField, (int) $request->query('tahun'));
        }

        return $this->respondList($query, $request);
    }

    public function store(Request $request)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $data = $request->validate([
            'wilayah' => ['required', 'string', 'max:80'],
            'nama_siswa' => ['required', 'string', 'max:120'],
            'ttl' => ['nullable', 'string', 'max:120'],
            'asal_sekolah' => ['required', 'string', 'max:120'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:30'],
            'nama_medsos' => ['nullable', 'string', 'max:120'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'asal_kota' => ['nullable', 'string', 'max:120'],
            'program' => ['nullable', 'string', 'max:120'],
            'nama_ortu' => ['nullable', 'string', 'max:120'],
            'no_tlp_ortu' => ['nullable', 'string', 'max:30'],
            'tanggal_input' => ['nullable', 'date'],
            'info_villa_merah_dari' => ['nullable', 'array'],
            'info_villa_merah_dari.*' => ['string'],
            'platform_medsos' => ['nullable', 'array'],
            'platform_medsos.*' => ['string'],
            'status_crm' => ['nullable', 'string', 'max:20'],
            'tgl_followup' => ['nullable', 'date'],
            'tanggal_closing' => ['nullable', 'date'],
            'catatan_crm' => ['nullable', 'string'],
            'program_pendidikan' => ['nullable', 'string', 'max:120'],
            'is_closing' => ['nullable', 'boolean'],
        ]);

        $lead = DataLead::create($data);

        return response()->json($lead, 201);
    }

    public function show(DataLead $dataLead)
    {
        return response()->json($dataLead);
    }

    public function update(Request $request, DataLead $dataLead)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $data = $request->validate([
            'wilayah' => ['sometimes', 'string', 'max:80'],
            'nama_siswa' => ['sometimes', 'string', 'max:120'],
            'ttl' => ['sometimes', 'nullable', 'string', 'max:120'],
            'asal_sekolah' => ['sometimes', 'string', 'max:120'],
            'kelas' => ['sometimes', 'nullable', 'string', 'max:50'],
            'no_hp' => ['sometimes', 'string', 'max:30'],
            'nama_medsos' => ['sometimes', 'nullable', 'string', 'max:120'],
            'alamat' => ['sometimes', 'nullable', 'string', 'max:255'],
            'asal_kota' => ['sometimes', 'nullable', 'string', 'max:120'],
            'program' => ['sometimes', 'nullable', 'string', 'max:120'],
            'nama_ortu' => ['sometimes', 'nullable', 'string', 'max:120'],
            'no_tlp_ortu' => ['sometimes', 'nullable', 'string', 'max:30'],
            'tanggal_input' => ['sometimes', 'nullable', 'date'],
            'info_villa_merah_dari' => ['sometimes', 'nullable', 'array'],
            'info_villa_merah_dari.*' => ['string'],
            'platform_medsos' => ['sometimes', 'nullable', 'array'],
            'platform_medsos.*' => ['string'],
            'status_crm' => ['sometimes', 'nullable', 'string', 'max:20'],
            'tgl_followup' => ['sometimes', 'nullable', 'date'],
            'tanggal_closing' => ['sometimes', 'nullable', 'date'],
            'catatan_crm' => ['sometimes', 'nullable', 'string'],
            'program_pendidikan' => ['sometimes', 'nullable', 'string', 'max:120'],
            'is_closing' => ['sometimes', 'boolean'],
        ]);

        $dataLead->fill($data)->save();

        return response()->json($dataLead);
    }

    public function destroy(Request $request, DataLead $dataLead)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $dataLead->delete();

        return response()->json(['message' => 'Data lead dihapus.']);
    }

    private function respondList($query, Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    private function resolveDateField(Request $request, array $allowed, string $fallback): string
    {
        $requested = $request->query('date_field');

        if ($requested && in_array($requested, $allowed, true)) {
            return $requested;
        }

        return $fallback;
    }
}
