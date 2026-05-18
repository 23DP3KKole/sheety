<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Queries\AdminQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        return response()->json(['users' => AdminQuery::usersWithRoles()]);
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
        return response()->json(['tabs' => AdminQuery::tabsWithUploaders()]);
    }
}
