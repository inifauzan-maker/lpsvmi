<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TargetMarket;
use Illuminate\Http\Request;

class TargetMarketController extends Controller
{
    public function index(Request $request)
    {
        $query = TargetMarket::query()->orderByDesc('id');

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->query('wilayah'));
        }
        if ($request->filled('program')) {
            $query->where('program', $request->query('program'));
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', (int) $request->query('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', (int) $request->query('tahun'));
        }

        return $this->respondList($query, $request);
    }

    public function store(Request $request)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $data = $request->validate([
            'wilayah' => ['required', 'string', 'max:80'],
            'program' => ['required', 'string', 'max:120'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'target_siswa' => ['required', 'integer', 'min:0'],
            'target_omset' => ['required', 'numeric', 'min:0'],
        ]);

        $target = TargetMarket::create($data);

        return response()->json($target, 201);
    }

    public function show(TargetMarket $targetMarket)
    {
        return response()->json($targetMarket);
    }

    public function update(Request $request, TargetMarket $targetMarket)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $data = $request->validate([
            'wilayah' => ['sometimes', 'string', 'max:80'],
            'program' => ['sometimes', 'string', 'max:120'],
            'bulan' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'tahun' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'target_siswa' => ['sometimes', 'integer', 'min:0'],
            'target_omset' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $targetMarket->fill($data)->save();

        return response()->json($targetMarket);
    }

    public function destroy(Request $request, TargetMarket $targetMarket)
    {
        $this->requireRole($request, ['admin', 'super_admin']);

        $targetMarket->delete();

        return response()->json(['message' => 'Target market dihapus.']);
    }

    private function respondList($query, Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }
}
