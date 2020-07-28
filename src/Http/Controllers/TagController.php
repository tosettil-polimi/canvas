<?php

namespace Canvas\Http\Controllers;

use Canvas\Models\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            Tag::latest()
               ->withCount('posts')
               ->paginate(), 200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return response()->json(Tag::make([
            'id' => Uuid::uuid4()->toString(),
        ]), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function store($id): JsonResponse
    {
        $tag = Tag::find($id);

        if (! $tag) {
            if ($tag = Tag::onlyTrashed()->firstWhere('slug', request('slug'))) {
                $tag->restore();
            } else {
                $tag = new Tag(['id' => $id]);
            }
        }

        $data = [
            'id' => $id,
            'name' => request('name'),
            'slug' => request('slug'),
            'user_id' => request()->user()->id,
        ];

        $rules = [
            'name' => 'required',
            'slug' => [
                'required',
                'alpha_dash',
                Rule::unique('canvas_tags')->where(function ($query) use ($data) {
                    return $query->where('slug', $data['slug'])->where('user_id', $data['user_id']);
                })->ignore($id)->whereNull('deleted_at'),
            ],
        ];

        $messages = [
            'required' => trans('canvas::app.validation_required', [], optional($tag->userMeta)->locale),
            'unique' => trans('canvas::app.validation_unique', [], optional($tag->userMeta)->locale),
        ];

        validator($data, $rules, $messages)->validate();

        $tag->fill($data);

        $tag->save();

        return response()->json($tag->refresh(), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function show($id): JsonResponse
    {
        $tag = Tag::find($id);

        return $tag ? response()->json($tag, 200) : response()->json(null, 404);
    }

    /**
     * Display the specified relationship.
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function showPosts($id): JsonResponse
    {
        $tag = Tag::with('posts')->find($id);

        return $tag ? response()->json($tag->posts()->withCount('views')->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        $tag->delete();

        return response()->json(null, 204);
    }
}
