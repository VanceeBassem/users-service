<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
       public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'nullable|in:admin,seller,customer'
        ]);

        $role = Role::where('name', $data['role'] ?? 'customer')->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role?->id
        ]);
        $user->load('role');

        $token = $this->issueToken($user);
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $r)
    {
        $r->validate(['email' => 'required|email', 'password' => 'required']);

        $user = User::with('role')->where('email', $r->email)->first();
        if (!$user || !Hash::check($r->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $this->issueToken($user);
        return response()->json(['user' => $user, 'token' => $token]);
    }

    protected function issueToken(User $user)
    {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'role' => $user->role?->name,
            'iat' => time(),
            'exp' => time() + 60*60*24*7 // 7 days
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    // returns current authenticated user (via jwt middleware)
    public function me(Request $r)
    {
        return response()->json($r->user());
    }

    // admin only: list users
    public function index()
    {
        $users = User::with('role')->paginate(20);
        return response()->json($users);
    }

    // admin only: assign role to a user
    public function assignRole(Request $r, User $user)
    {
        $r->validate(['role' => 'required|in:admin,seller,customer']);
        $role = Role::where('name', $r->role)->first();
        if (!$role) return response()->json(['message' => 'Role not found'], 422);
        $user->role_id = $role->id;
        $user->save();
        return response()->json($user->load('role'));
    }
}
