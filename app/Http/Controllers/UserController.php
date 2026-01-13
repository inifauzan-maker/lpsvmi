<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $users = User::query()->orderBy('id')->get();

        return response()->json($users->map(fn (User $user) => $this->formatUser($user)));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:3'],
            'role' => ['required', 'string', 'in:user,admin,super admin'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $role = $request->input('role');
        if ($currentUser->role === 'admin' && in_array($role, ['admin', 'super admin'], true)) {
            return response()->json(['message' => 'Admin tidak dapat membuat role ini.'], 403);
        }

        $user = User::query()->create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role' => $role,
        ]);

        return response()->json([
            'isOk' => true,
            'user' => $this->formatUser($user),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $currentUser = Auth::user();
        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $user = User::query()->findOrFail($id);
        if ($currentUser->role === 'admin') {
            if ($user->role === 'super admin' || $user->id === $currentUser->id) {
                return response()->json(['message' => 'Admin tidak dapat mengubah data ini.'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:3'],
            'role' => ['required', 'string', 'in:user,admin,super admin'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $role = $request->input('role');
        if ($currentUser->role === 'admin' && in_array($role, ['admin', 'super admin'], true)) {
            return response()->json(['message' => 'Admin tidak dapat mengubah role ini.'], 403);
        }

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->role = $role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        return response()->json([
            'isOk' => true,
            'user' => $this->formatUser($user),
        ]);
    }

    public function destroy(string $id)
    {
        $currentUser = Auth::user();
        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $user = User::query()->findOrFail($id);
        if ($currentUser->role === 'admin') {
            if ($user->role === 'super admin' || $user->id === $currentUser->id) {
                return response()->json(['message' => 'Admin tidak dapat menghapus akun ini.'], 403);
            }
        }

        $user->delete();

        return response()->json(['isOk' => true]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
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
