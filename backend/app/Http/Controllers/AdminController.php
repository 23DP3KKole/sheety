<?php

namespace App\Http\Controllers;

use App\Models\Tab;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        $users = User::with('role')->orderBy('name')->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name,
            'created_at' => $user->created_at,
        ]);

        return response()->json(['users' => $users]);
    }

    public function destroyUser(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    public function tabs(): JsonResponse
    {
        $tabs = Tab::with('user:id,name,email')->latest()->get();

        return response()->json(['tabs' => $tabs]);
    }
}
