<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Our business')</title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; max-width: 760px; margin: 0 auto; padding: 0; color: #2b2320; background: #fdfaf4; line-height: 1.7; }
        header { background: #b5563c; color: #fff; padding: 28px 24px; font-family: system-ui, sans-serif; font-weight: 700; letter-spacing: 0.02em; }
        main { padding: 32px 24px; }
        h1 { font-size: 30px; margin-bottom: 8px; color: #7a3524; }
        footer { padding: 16px 24px 32px; font-size: 13px; color: #9a8a7a; font-family: system-ui, sans-serif; }
    </style>
</head>
<body>
    <header>Open for business</header>
    <main>{!! $content !!}</main>
    <footer>Built on Moduloom · theme: small-business-default</footer>
</body>
</html>
