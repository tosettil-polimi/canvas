<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} ― Canvas</title>

    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css', 'vendor/canvas') }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Karla|Merriweather:400,700">
    <link rel="shortcut icon" href="{{ mix('favicon.ico', 'vendor/canvas') }}">
</head>
<body class="mb-5" @if($config['user']['darkMode']) data-theme="dark" @endif>
<div id="canvas">
    @if(!\Canvas\Helpers\Asset::upToDate())
       <div class="alert alert-danger border-0 text-center rounded-0 mb-0">
           {{ trans('canvas::app.assets_are_not_up_to_date') }}
           {{ trans('canvas::app.to_update_run') }}<br/><code>php artisan canvas:publish</code>
       </div>
    @endif

    <router-view></router-view>
</div>

<script>
    window.Canvas = @json($config);
</script>

<script type="text/javascript" src="{{ mix('js/app.js', 'vendor/canvas') }}"></script>
</body>
</html>
