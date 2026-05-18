<?php

namespace App\Http\Controllers;

use App\Queries\FavoriteQuery;
use App\Queries\TabQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        return response()->json([
            'favorites' => FavoriteQuery::tabsForUser($userId),
            'my_tabs' => TabQuery::forUser($userId),
        ]);
    }
}
