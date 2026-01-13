<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Student;
use App\Models\TargetMarket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $user = Auth::user();
        if (!$this->isAdmin($user)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $records = $request->input('records', []);
        $users = $request->input('users', []);
        $logs = $request->input('logs', []);

        $result = DB::transaction(function () use ($records, $users, $logs) {
            $recordCount = 0;
            $userCount = 0;
            $logCount = 0;

            foreach ($records as $record) {
                if (!is_array($record)) {
                    continue;
                }

                $module = $record['module'] ?? null;
                if (!in_array($module, ['data-siswa', 'data-leads', 'target-market'], true)) {
                    continue;
                }

                unset($record['module'], $record['__backendId']);
                if ($module === 'data-siswa') {
                    Student::query()->create($this->mapStudent($record));
                } elseif ($module === 'data-leads') {
                    Lead::query()->create($this->mapLead($record));
                } elseif ($module === 'target-market') {
                    TargetMarket::query()->create($this->mapTargetMarket($record));
                }
                $recordCount++;
            }

            foreach ($users as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $username = $entry['username'] ?? null;
                $password = $entry['password'] ?? null;
                if (!$username || !$password) {
                    continue;
                }

                $role = $entry['role'] ?? 'user';
                if (!in_array($role, ['user', 'admin', 'super admin'], true)) {
                    $role = 'user';
                }

                User::query()->updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $entry['name'] ?? $username,
                        'password' => Hash::make($password),
                        'role' => $role,
                    ]
                );
                $userCount++;
            }

            foreach ($logs as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $action = $entry['action'] ?? null;
                if (!$action) {
                    continue;
                }

                $timestamp = $entry['timestamp'] ?? null;
                $createdAt = $timestamp ? Carbon::createFromTimestampMs((int) $timestamp) : now();

                ActivityLog::query()->create([
                    'user_id' => null,
                    'user_name' => $entry['userName'] ?? 'Unknown',
                    'role' => $entry['role'] ?? 'user',
                    'action' => $action,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $logCount++;
            }

            return [
                'records' => $recordCount,
                'users' => $userCount,
                'logs' => $logCount,
            ];
        });

        return response()->json([
            'isOk' => true,
            'imported' => $result,
        ]);
    }

    private function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array($user->role, ['admin', 'super admin'], true);
    }

    private function mapStudent(array $record): array
    {
        return [
            'nama' => $record['nama'] ?? null,
            'ttl' => $record['ttl'] ?? null,
            'no_hp_siswa' => $record['no_hp_siswa'] ?? null,
            'asal_sekolah' => $record['asal_sekolah'] ?? null,
            'kelas' => $record['kelas'] ?? null,
            'asal_kota' => $record['asal_kota'] ?? null,
            'program' => $record['program'] ?? null,
            'nama_medsos' => $record['nama_medsos'] ?? null,
            'platform_medsos' => $this->normalizeArray($record['platform_medsos'] ?? null),
            'info_villa_merah_dari' => $this->normalizeArray($record['info_villa_merah_dari'] ?? null),
            'alamat_siswa' => $record['alamat_siswa'] ?? null,
            'nama_ortu' => $record['nama_ortu'] ?? null,
            'no_tlp_ortu' => $record['no_tlp_ortu'] ?? null,
            'biaya_pendidikan' => $this->toInt($record['biaya_pendidikan'] ?? null),
            'sisa_angsuran' => $this->toInt($record['sisa_angsuran'] ?? null),
            'location' => $record['location'] ?? null,
            'from_leads_id' => $record['fromLeadsId'] ?? null,
            'tanggal_daftar' => $this->parseDate($record['createdAt'] ?? $record['tanggal_daftar'] ?? null),
        ];
    }

    private function mapLead(array $record): array
    {
        $tanggalInput = $record['tanggal_input'] ?? $record['createdAt'] ?? null;

        return [
            'nama_siswa' => $record['nama_siswa'] ?? null,
            'ttl' => $record['ttl'] ?? null,
            'program' => $record['program'] ?? null,
            'asal_sekolah' => $record['asal_sekolah'] ?? null,
            'kelas' => $record['kelas'] ?? null,
            'asal_kota' => $record['asal_kota'] ?? null,
            'no_hp' => $record['no_hp'] ?? null,
            'nama_medsos' => $record['nama_medsos'] ?? null,
            'nama_ortu' => $record['nama_ortu'] ?? null,
            'no_tlp_ortu' => $record['no_tlp_ortu'] ?? null,
            'tanggal_input' => $this->parseDate($tanggalInput),
            'alamat' => $record['alamat'] ?? null,
            'wilayah' => $record['wilayah'] ?? null,
            'platform_medsos' => $this->normalizeArray($record['platform_medsos'] ?? null),
            'info_villa_merah_dari' => $this->normalizeArray($record['info_villa_merah_dari'] ?? null),
            'tgl_followup' => $this->parseDate($record['tgl_followup'] ?? null),
            'status_crm' => $record['status_crm'] ?? null,
            'tanggal_closing' => $this->parseDate($record['tanggal_closing'] ?? null),
            'catatan_crm' => $record['catatan_crm'] ?? null,
            'program_pendidikan' => $record['program_pendidikan'] ?? null,
            'is_closing' => filter_var($record['is_closing'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private function mapTargetMarket(array $record): array
    {
        return [
            'wilayah' => $record['wilayah'] ?? null,
            'program' => $record['program'] ?? null,
            'bulan' => $this->toInt($record['bulan'] ?? null),
            'tahun' => $this->toInt($record['tahun'] ?? null),
            'target_siswa' => $this->toInt($record['target_siswa'] ?? null),
            'target_omset' => $this->toInt($record['target_omset'] ?? null),
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
}
