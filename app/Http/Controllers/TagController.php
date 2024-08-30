<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all() ,[
            'name' => ['required', 'unique:tags'],
        ]);

        if ($validator->fails()) {
            return response()->json([
               'status' => 'failed',
               'errors' => $validator->errors(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tag added successfully!',
            'data' => Tag::create($validator->validated()),
        ]);
    }

    public function show(Tag $tag)
    {
        return $tag;
    }

    public function update(Request $request, Tag $tag)
    {
        $validator = \Validator::make($request->all() ,[
            'name' => ['required', 'unique:tags,name,'.$tag->id],
        ]);

        if ($validator->fails()) {
            return response()->json([
               'status' => 'failed',
               'errors' => $validator->errors(),
            ]);
        }

        $tag->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Tag updated successfully!',
            'data' => $tag,
        ]);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tag deleted successfully!',
        ]);
    }
}
