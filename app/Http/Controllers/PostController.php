<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected Authenticatable $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function index()
    {
        return $this->user->posts()->orderBy('pinned', 'desc')->get();
    }

    public function show(Post $post)
    {
        \Gate::authorize('view', $post);
        return $post;
    }

    public function update(Request $request, Post $post)
    {
        \Gate::authorize('update', $post);
        $validator = \Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image'],
            'pinned' => ['required', 'boolean'],
            'tags' => ['required', 'array', 'min:1', 'exists:tags,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ]);
        }

        $data = $validator->validated();

        if (isset($data['cover_image'])) {
            \Storage::disk('public')->delete($post->cover_image);

            $coverImage = $request->file('cover_image')->store('covers', 'public');

            if (!$coverImage) {
                return response()->json([
                    "status" => "error",
                    "message" => "Can't store image!",
                ], 500);
            }

            $data['cover_image'] = $coverImage;
        }

        $post->update($data);
        $post->tags()->sync($data['tags']);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully!',
            'data' => $post->load('tags'),
        ]);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'cover_image' => ['required', 'image'],
            'pinned' => ['required', 'boolean'],
            'tags' => ['required', 'array', 'min:1', 'exists:tags,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $coverImage = $request->file('cover_image')->store('covers', 'public');

        if (!$coverImage) {
            return response()->json([
                "status" => "error",
                "message" => "Can't store image!",
            ], 500);
        }

        $data['cover_image'] = $coverImage;

        $post = $this->user->posts()->create($data);
        $post->tags()->sync($data['tags']);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully!',
            'data' => $post->load('tags'),
        ]);
    }

    public function destroy(Post $post)
    {
        \Gate::authorize('delete', $post);
        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully!',
        ]);
    }

    public function trash()
    {
        return $this->user->posts()->onlyTrashed()->orderBy('pinned', 'desc')->get();
    }

    public function restore($id)
    {
        \Gate::authorize('restore', Post::class);
        $post = Post::withTrashed()->find($id);

        if (!$post->deleted_at) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Post not in trash!',
            ], 422);
        }

        $post->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Post restored successfully!',
        ]);
    }
}
