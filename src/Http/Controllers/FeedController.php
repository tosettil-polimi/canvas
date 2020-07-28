<?php

namespace Canvas\Http\Controllers;

use Canvas\Models\Post;
use Illuminate\Routing\Controller;

class FeedController extends Controller
{
    /**
     * Return the RSS feed.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke()
    {
        $data = [
            'name' => config('app.name'),
            'link' => url(config('canvas.feed.path')),
            'data' => Post::published()->with('user')->get()->toArray(),
            'updated' => now()->toDateTimeString(),
        ];

        $content = view('canvas::rss.feed', compact('data'));

        return response($content, 200)->header('Content-Type', 'text/xml');
    }
}
