<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/brand/moduloom-mark.svg">
    <title>Federation — Moduloom</title>
    @vite(['resources/css/app.css', 'resources/js/shell/federation-entry.tsx'])
</head>
<body>
    <div id="shell-root"></div>
</body>
</html>
