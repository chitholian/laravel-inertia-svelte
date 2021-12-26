<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Inertia Svelte</title>
    <base id="base_url" href="{{ env('APP_PREFIX') }}">
    <link href="{{ rtrim(env('APP_PREFIX'), '/') . mix('assets/css/app.css') }}" rel="stylesheet"/>
    <script src="{{ rtrim(env('APP_PREFIX'), '/') . mix('assets/js/manifest.js') }}"></script>
    <script src="{{ rtrim(env('APP_PREFIX'), '/') . mix('assets/js/vendor.js') }}"></script>
    <script src="{{ rtrim(env('APP_PREFIX'), '/') . mix('assets/js/app.js') }}" defer></script>
    <link href="assets/font-awesome/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
@inertia
</body>
</html>
