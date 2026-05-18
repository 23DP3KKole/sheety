<?php

namespace App\Http\Controllers;

use App\Models\Tab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favorites()
            ->with('user:id,name')
            ->latest('favorites.created_at')
            ->get();

        return response()->json(['favorites' => $favorites]);
    }

    public function store(Request $request, Tab $tab): JsonResponse
    {
        $request->user()->favorites()->syncWithoutDetaching([$tab->id]);

        return response()->json(['message' => 'Added to favorites.'], 201);
    }

    public function destroy(Request $request, Tab $tab): JsonResponse
    {
        $request->user()->favorites()->detach($tab->id);

        return response()->json(['message' => 'Removed from favorites.']);
    }
}
