<?php

namespace Canvas\Http\Controllers;

use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    /**
     * Return the app translations for a given locale code.
     *
     * @param string $code
     * @return string
     */
    public function __invoke(string $code): string
    {
        return collect(['app' => trans('canvas::app', [], $code)])->toJson();
    }
}
