<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class CommentQuery
{
    public static function forTab(int $tabId, ?int $viewerUserId = null): array
    {
        $query = DB::table('comments as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('comment_votes as cv', 'cv.comment_id', '=', 'c.id')
            ->where('c.tab_id', $tabId)
            ->groupBy(
                'c.id',
                'c.tab_id',
                'c.user_id',
                'c.body',
                'c.created_at',
                'c.updated_at',
                'u.name'
            )
            ->select([
                'c.id',
                'c.tab_id',
                'c.user_id',
                'c.body',
                'c.created_at',
                'c.updated_at',
                'u.name as user_name',
            ])
            ->selectRaw('COALESCE(SUM(CASE WHEN cv.value = 1 THEN 1 ELSE 0 END), 0) as upvotes')
            ->selectRaw('COALESCE(SUM(CASE WHEN cv.value = -1 THEN 1 ELSE 0 END), 0) as downvotes');

        if ($viewerUserId) {
            $query->selectSub(
                DB::table('comment_votes')
                    ->select('value')
                    ->whereColumn('comment_id', 'c.id')
                    ->where('user_id', $viewerUserId)
                    ->limit(1),
                'user_vote'
            );
        }

        return $query
            ->orderByDesc('upvotes')
            ->orderBy('downvotes')
            ->orderByDesc('c.created_at')
            ->get()
            ->map(fn ($row) => self::format($row))
            ->all();
    }

    public static function findWithUserAndVotes(int $commentId, ?int $viewerUserId = null): ?array
    {
        $query = DB::table('comments as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('comment_votes as cv', 'cv.comment_id', '=', 'c.id')
            ->where('c.id', $commentId)
            ->groupBy(
                'c.id',
                'c.tab_id',
                'c.user_id',
                'c.body',
                'c.created_at',
                'c.updated_at',
                'u.name'
            )
            ->select([
                'c.id',
                'c.tab_id',
                'c.user_id',
                'c.body',
                'c.created_at',
                'c.updated_at',
                'u.name as user_name',
            ])
            ->selectRaw('COALESCE(SUM(CASE WHEN cv.value = 1 THEN 1 ELSE 0 END), 0) as upvotes')
            ->selectRaw('COALESCE(SUM(CASE WHEN cv.value = -1 THEN 1 ELSE 0 END), 0) as downvotes');

        if ($viewerUserId) {
            $query->selectSub(
                DB::table('comment_votes')
                    ->select('value')
                    ->whereColumn('comment_id', 'c.id')
                    ->where('user_id', $viewerUserId)
                    ->limit(1),
                'user_vote'
            );
        }

        $row = $query->first();

        return $row ? self::format($row) : null;
    }

    public static function format(object $row): array
    {
        return [
            'id' => $row->id,
            'tab_id' => $row->tab_id,
            'body' => $row->body,
            'user' => [
                'id' => $row->user_id,
                'name' => $row->user_name,
            ],
            'upvotes' => (int) $row->upvotes,
            'downvotes' => (int) $row->downvotes,
            'user_vote' => isset($row->user_vote) ? (int) $row->user_vote : null,
            'created_at' => $row->created_at,
        ];
    }
}
