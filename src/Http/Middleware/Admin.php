<?php

namespace Canvas\Http\Middleware;

use Canvas\Models\UserMeta;
use Closure;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle the incoming request.
     *
     * @param $request
     * @param $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $meta = UserMeta::firstWhere('user_id', $request->user()->id);

        return optional($meta)->admin == 1 ? $next($request) : response()->json(null, 403);
    }
}
