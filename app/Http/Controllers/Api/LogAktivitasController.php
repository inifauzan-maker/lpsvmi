<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $this->requireRole($request, ['super_admin']);

        $query = LogAktivitas::query()->orderByDesc('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->query('tanggal'));
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', (int) $request->query('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', (int) $request->query('tahun'));
        }

        return $this->respondList($query, $request);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'user_name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:20'],
            'action' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'hari' => ['required', 'string', 'max:20'],
            'waktu' => ['required', 'date_format:H:i:s'],
            'timestamp_ms' => ['nullable', 'integer'],
        ]);

        $data = $this->normalizeTimestamp($data);
        $log = LogAktivitas::create($data);

        return response()->json($log, 201);
    }

    public function show(Request $request, LogAktivitas $logAktivitas)
    {
        $this->requireRole($request, ['super_admin']);

        return response()->json($logAktivitas);
    }

    public function update(Request $request, LogAktivitas $logAktivitas)
    {
        $this->requireRole($request, ['super_admin']);

        $data = $request->validate([
            'user_id' => ['sometimes', 'nullable', 'integer'],
            'user_name' => ['sometimes', 'string', 'max:100'],
            'role' => ['sometimes', 'string', 'max:20'],
            'action' => ['sometimes', 'string', 'max:255'],
            'tanggal' => ['sometimes', 'date'],
            'hari' => ['sometimes', 'string', 'max:20'],
            'waktu' => ['sometimes', 'date_format:H:i:s'],
            'timestamp_ms' => ['sometimes', 'nullable', 'integer'],
        ]);

        $data = $this->normalizeTimestamp($data);
        $logAktivitas->fill($data)->save();

        return response()->json($logAktivitas);
    }

    public function destroy(Request $request, LogAktivitas $logAktivitas)
    {
        $this->requireRole($request, ['super_admin']);

        $logAktivitas->delete();

        return response()->json(['message' => 'Log aktivitas dihapus.']);
    }

    private function respondList($query, Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    private function normalizeTimestamp(array $data): array
    {
        if (!array_key_exists('timestamp_ms', $data) || $data['timestamp_ms'] === null) {
            return $data;
        }

        $value = (int) $data['timestamp_ms'];
        if ($value > 2147483647) {
            $value = (int) floor($value / 1000);
        }

        $data['timestamp_ms'] = $value;

        return $data;
    }
}
