<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->requireRole($request, ['super_admin']);

        $query = User::query()->orderByDesc('id');

        return $this->respondList($query, $request);
    }

    public function store(Request $request)
    {
        $this->requireRole($request, ['super_admin']);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role' => ['nullable', 'string', 'in:admin,user'],
            'password' => ['required', 'string', 'min:3'],
        ]);

        if (empty($data['email'])) {
            $data['email'] = $data['username'].'@local.test';
        }

        $data['role'] = $data['role'] ?? 'user';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(Request $request, User $user)
    {
        $this->requireRole($request, ['super_admin']);

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $this->requireRole($request, ['super_admin']);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'username' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['sometimes', 'string', 'in:admin,user'],
            'password' => ['sometimes', 'string', 'min:3'],
        ]);

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data)->save();

        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        $this->requireRole($request, ['super_admin']);

        $user->delete();

        return response()->json(['message' => 'User dihapus.']);
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
