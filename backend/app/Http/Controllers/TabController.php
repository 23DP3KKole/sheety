<?php

namespace App\Http\Controllers;

use App\Models\Tab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TabController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tab::query()->with('user:id,name');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('artist', 'like', "%{$search}%");
            });
        }

        $tabs = $query->latest()->paginate(20);

        return response()->json($tabs);
    }

    public function show(Tab $tab): JsonResponse
    {
        $tab->load('user:id,name');

        return response()->json(['tab' => $tab]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $tab = $request->user()->tabs()->create($validated);
        $tab->load('user:id,name');

        return response()->json(['tab' => $tab], 201);
    }

    public function update(Request $request, Tab $tab): JsonResponse
    {
        if ($tab->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'artist' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
        ]);

        $tab->update($validated);
        $tab->load('user:id,name');

        return response()->json(['tab' => $tab]);
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
