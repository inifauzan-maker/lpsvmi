<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index()
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $logs = ActivityLog::query()
            ->orderByDesc('id')
            ->get();

        return response()->json($logs->map(fn (ActivityLog $log) => $this->formatLog($log)));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $action = $request->input('action');
        if (!$action) {
            return response()->json(['message' => 'Aksi wajib diisi.'], 422);
        }

        $log = ActivityLog::query()->create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'action' => $action,
        ]);

        return response()->json([
            'isOk' => true,
            'data' => $this->formatLog($log),
        ]);
    }

    public function clear()
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        ActivityLog::query()->delete();

        return response()->json(['isOk' => true]);
    }

    private function formatLog(ActivityLog $log): array
    {
        $createdAt = $log->created_at ?? now();
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return [
            'userName' => $log->user_name,
            'role' => $log->role,
            'action' => $log->action,
            'date' => $createdAt->format('d/m/Y'),
            'day' => $days[$createdAt->dayOfWeek],
            'time' => $createdAt->format('H:i:s'),
            'timestamp' => $createdAt->timestamp * 1000,
        ];
    }

    private function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array($user->role, ['admin', 'super admin'], true);
    }
}
