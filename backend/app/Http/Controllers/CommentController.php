<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Tab;
use App\Queries\CommentQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function index(Request $request, Tab $tab): JsonResponse
    {
        return response()->json([
            'comments' => CommentQuery::forTab($tab->id, $request->user()?->id),
        ]);
    }

    public function store(StoreCommentRequest $request, Tab $tab): JsonResponse
    {
        $validated = $request->validated();

        $comment = $tab->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $formatted = CommentQuery::findWithUserAndVotes($comment->id, $request->user()->id);

        return response()->json(['comment' => $formatted], 201);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted.']);
    }

    public function vote(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'integer', Rule::in([1, -1])],
        ]);

        $vote = CommentVote::query()
            ->where('comment_id', $comment->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($vote && $vote->value === $validated['value']) {
            $vote->delete();
        } elseif ($vote) {
            $vote->update(['value' => $validated['value']]);
        } else {
            CommentVote::create([
                'comment_id' => $comment->id,
                'user_id' => $request->user()->id,
                'value' => $validated['value'],
            ]);
        }

        $formatted = CommentQuery::findWithUserAndVotes($comment->id, $request->user()->id);

        return response()->json(['comment' => $formatted]);
    }
}
