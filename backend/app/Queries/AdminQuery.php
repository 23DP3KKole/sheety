<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class AdminQuery
{
    public static function usersWithRoles(): array
    {
        return DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->orderBy('users.name')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                'roles.name as role',
            ])
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'role' => $row->role,
                'created_at' => $row->created_at,
            ])
            ->all();
    }

    public static function tabsWithUploaders(): array
    {
        return DB::table('tabs')
            ->join('users', 'users.id', '=', 'tabs.user_id')
            ->orderByDesc('tabs.created_at')
            ->select([
                'tabs.id',
                'tabs.user_id',
                'tabs.title',
                'tabs.artist',
                'tabs.content',
                'tabs.created_at',
                'tabs.updated_at',
                'users.name as uploader_name',
                'users.email as uploader_email',
            ])
            ->get()
            ->map(fn ($row) => array_merge(TabQuery::format($row), [
                'user' => [
                    'id' => $row->user_id,
                    'name' => $row->uploader_name,
                    'email' => $row->uploader_email,
                ],
            ]))
            ->all();
    }
}
