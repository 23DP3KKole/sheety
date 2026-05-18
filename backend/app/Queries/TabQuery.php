<?php

namespace App\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabQuery
{
    public static function joined(): Builder
    {
        return DB::table('tabs')
            ->join('users', 'users.id', '=', 'tabs.user_id')
            ->select([
                'tabs.id',
                'tabs.user_id',
                'tabs.title',
                'tabs.artist',
                'tabs.content',
                'tabs.created_at',
                'tabs.updated_at',
                'users.name as uploader_name',
            ]);
    }

    public static function paginateWithUser(Request $request): LengthAwarePaginator
    {
        $query = self::joined();

        if ($search = $request->query('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('tabs.title', 'like', "%{$search}%")
                    ->orWhere('tabs.artist', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc'));

        $allowedSort = ['title', 'artist', 'created_at'];
        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        $column = str_contains($sortBy, '.') ? $sortBy : "tabs.{$sortBy}";

        $paginator = $query->orderBy($column, $sortDir)->paginate(20);

        return $paginator->through(fn ($row) => self::format($row));
    }

    public static function findWithUser(int $tabId): ?array
    {
        $row = self::joined()->where('tabs.id', $tabId)->first();

        return $row ? self::format($row) : null;
    }

    public static function forUser(int $userId): array
    {
        return self::joined()
            ->where('tabs.user_id', $userId)
            ->orderByDesc('tabs.created_at')
            ->get()
            ->map(fn ($row) => self::format($row))
            ->all();
    }

    public static function format(object $row): array
    {
        return [
            'id' => $row->id,
            'user_id' => $row->user_id,
            'title' => $row->title,
            'artist' => $row->artist,
            'content' => $row->content,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
            'user' => [
                'id' => $row->user_id,
                'name' => $row->uploader_name,
            ],
        ];
    }
}
