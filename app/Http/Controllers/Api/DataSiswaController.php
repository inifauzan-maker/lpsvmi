<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataSiswa;
use Illuminate\Http\Request;

class DataSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = DataSiswa::query()->orderByDesc('id');
        $dateField = $this->resolveDateField($request, ['tanggal_daftar', 'created_at'], 'created_at');

        if ($request->filled('location')) {
            $query->where('location', $request->query('location'));
        }
        if ($request->filled('program')) {
            $query->where('program', $request->query('program'));
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
            'location' => ['required', 'string', 'max:80'],
            'nama' => ['required', 'string', 'max:120'],
            'ttl' => ['nullable', 'string', 'max:120'],
            'no_hp_siswa' => ['required', 'string', 'max:30'],
            'asal_sekolah' => ['required', 'string', 'max:120'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'asal_kota' => ['nullable', 'string', 'max:120'],
            'program' => ['nullable', 'string', 'max:120'],
            'nama_medsos' => ['nullable', 'string', 'max:120'],
            'platform_medsos' => ['nullable', 'array'],
            'platform_medsos.*' => ['string'],
            'info_villa_merah_dari' => ['nullable', 'array'],
            'info_villa_merah_dari.*' => ['string'],
            'alamat_siswa' => ['nullable', 'string', 'max:255'],
            'nama_ortu' => ['nullable', 'string', 'max:120'],
            'no_tlp_ortu' => ['nullable', 'string', 'max:30'],
            'tanggal_daftar' => ['nullable', 'date'],
            'biaya_pendidikan' => ['nullable', 'numeric'],
            'sisa_angsuran' => ['nullable', 'numeric'],
        ]);

        $siswa = DataSiswa::create($data);

        return response()->json($siswa, 201);
    }

    public function show(DataSiswa $dataSiswa)
    {
        return response()->json($dataSiswa);
    }

    public function update(Request $request, DataSiswa $dataSiswa)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $data = $request->validate([
            'location' => ['sometimes', 'string', 'max:80'],
            'nama' => ['sometimes', 'string', 'max:120'],
            'ttl' => ['sometimes', 'nullable', 'string', 'max:120'],
            'no_hp_siswa' => ['sometimes', 'string', 'max:30'],
            'asal_sekolah' => ['sometimes', 'string', 'max:120'],
            'kelas' => ['sometimes', 'nullable', 'string', 'max:50'],
            'asal_kota' => ['sometimes', 'nullable', 'string', 'max:120'],
            'program' => ['sometimes', 'nullable', 'string', 'max:120'],
            'nama_medsos' => ['sometimes', 'nullable', 'string', 'max:120'],
            'platform_medsos' => ['sometimes', 'nullable', 'array'],
            'platform_medsos.*' => ['string'],
            'info_villa_merah_dari' => ['sometimes', 'nullable', 'array'],
            'info_villa_merah_dari.*' => ['string'],
            'alamat_siswa' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nama_ortu' => ['sometimes', 'nullable', 'string', 'max:120'],
            'no_tlp_ortu' => ['sometimes', 'nullable', 'string', 'max:30'],
            'tanggal_daftar' => ['sometimes', 'nullable', 'date'],
            'biaya_pendidikan' => ['sometimes', 'nullable', 'numeric'],
            'sisa_angsuran' => ['sometimes', 'nullable', 'numeric'],
        ]);

        $dataSiswa->fill($data)->save();

        return response()->json($dataSiswa);
    }

    public function destroy(Request $request, DataSiswa $dataSiswa)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $dataSiswa->delete();

        return response()->json(['message' => 'Data siswa dihapus.']);
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
