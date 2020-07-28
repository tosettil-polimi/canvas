<?php

namespace Canvas\Http\Controllers;

use Canvas\Models\Topic;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            Topic::latest()
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
        return response()->json(Topic::make([
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
        $topic = Topic::find($id);

        if (! $topic) {
            if ($topic = Topic::onlyTrashed()->firstWhere('slug', request('slug'))) {
                $topic->restore();
            } else {
                $topic = new Topic(['id' => $id]);
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
                Rule::unique('canvas_topics')->where(function ($query) use ($data) {
                    return $query->where('slug', $data['slug'])->where('user_id', $data['user_id']);
                })->ignore($id)->whereNull('deleted_at'),
            ],
        ];

        $messages = [
            'required' => trans('canvas::app.validation_required', [], optional($topic->userMeta)->locale),
            'unique' => trans('canvas::app.validation_unique', [], optional($topic->userMeta)->locale),
        ];

        validator($data, $rules, $messages)->validate();

        $topic->fill($data);

        $topic->save();

        return response()->json($topic->refresh(), 201);
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
        $topic = Topic::find($id);

        return $topic ? response()->json($topic, 200) : response()->json(null, 404);
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
        $topic = Topic::with('posts')->find($id);

        return $topic ? response()->json($topic->posts()->withCount('views')->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);

        $topic->delete();

        return response()->json(null, 204);
    }
}
