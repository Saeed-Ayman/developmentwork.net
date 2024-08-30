<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class StatusController extends Controller
{
    public function __invoke()
    {
        $status = [];

        $cacheKeys = [
            'users.count' => 'user.total',
            'users.zero-posts' => 'user.zero-posts',
            'posts.count' => 'posts.count',
        ];

        foreach ($cacheKeys as $keyName => $cacheKey) {
            if (\Cache::has($cacheKey)) {
                $status[$keyName] = \Cache::get($cacheKey);
            } else {
                $data = match ($keyName) {
                    'users.count' => User::count(),
                    'users.zero-posts' => User::doesntHave('posts')->count(),
                    'posts.count' => Post::count(),
                };
                $status[$keyName] = $data;
                \Cache::put($cacheKey, $data);
            }
        }


        return collect($status)->undot();
    }
}
