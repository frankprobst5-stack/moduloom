<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Moduloom site')</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 720px; margin: 0 auto; padding: 40px 24px; color: #1f2937; line-height: 1.6; }
        h1 { font-size: 28px; margin-bottom: 8px; }
        footer { margin-top: 48px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    {!! $content !!}
    <footer>Built on Moduloom · theme: default</footer>
</body>
</html>
