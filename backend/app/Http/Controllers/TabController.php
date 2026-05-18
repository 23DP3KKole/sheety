<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTabRequest;
use App\Http\Requests\UpdateTabRequest;
use App\Models\Tab;
use App\Queries\CommentQuery;
use App\Queries\TabQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TabController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(TabQuery::paginateWithUser($request));
    }

    public function show(Request $request, Tab $tab): JsonResponse
    {
        $tabData = TabQuery::findWithUser($tab->id);

        if (! $tabData) {
            return response()->json(['message' => 'Tab not found.'], 404);
        }

        $comments = CommentQuery::forTab($tab->id, $request->user()?->id);

        return response()->json([
            'tab' => $tabData,
            'comments' => $comments,
        ]);
    }

    public function store(StoreTabRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tab = $request->user()->tabs()->create($validated);

        $tabData = TabQuery::findWithUser($tab->id);

        return response()->json(['tab' => $tabData], 201);
    }

    public function update(UpdateTabRequest $request, Tab $tab): JsonResponse
    {
        if ($tab->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $tab->update($request->validated());

        return response()->json(['tab' => TabQuery::findWithUser($tab->id)]);
    }

    public function destroy(Request $request, Tab $tab): JsonResponse
    {
        if ($tab->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $tab->delete();

        return response()->json(['message' => 'Tab deleted.']);
    }
}
