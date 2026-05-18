<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class FavoriteQuery
{
    /**
     * favorites JOIN tabs JOIN users — combined tab + uploader for profile/list pages.
     */
    public static function tabsForUser(int $userId): array
    {
        return DB::table('favorites as f')
            ->join('tabs', 'tabs.id', '=', 'f.tab_id')
            ->join('users', 'users.id', '=', 'tabs.user_id')
            ->where('f.user_id', $userId)
            ->orderByDesc('f.created_at')
            ->select([
                'tabs.id',
                'tabs.user_id',
                'tabs.title',
                'tabs.artist',
                'tabs.content',
                'tabs.created_at',
                'tabs.updated_at',
                'users.name as uploader_name',
                'f.created_at as favorited_at',
            ])
            ->get()
            ->map(fn ($row) => array_merge(TabQuery::format($row), [
                'favorited_at' => $row->favorited_at,
            ]))
            ->all();
    }
}
