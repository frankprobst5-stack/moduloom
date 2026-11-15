<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/brand/moduloom-mark.svg">
    <title>Log in — Moduloom</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div style="max-width: 360px; margin: 100px auto; font-family: var(--brand-font-body);">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 28px;">
            <img src="/brand/moduloom-mark.svg" width="28" height="28" alt="">
            <span style="font-family: var(--brand-font-display); font-weight: 600; font-size: 22px; color: var(--brand-charcoal);">Moduloom</span>
        </div>

        <h1 style="font-family: var(--brand-font-display); font-weight: 600; font-size: 22px; margin-bottom: 20px;">Log in</h1>

        @if ($errors->any())
            <p style="color: var(--brand-danger); font-size: 13px; margin: 0 0 16px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="/login" style="display: flex; flex-direction: column; gap: 14px;">
            @csrf
            <div>
                <label for="email" style="display: block; font-size: 13px; color: var(--brand-muted); margin-bottom: 4px;">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 8px 10px; border: 1px solid var(--brand-border); border-radius: 6px; font-size: 14px; box-sizing: border-box;">
            </div>
            <div>
                <label for="password" style="display: block; font-size: 13px; color: var(--brand-muted); margin-bottom: 4px;">Password</label>
                <input id="password" name="password" type="password" required
                    style="width: 100%; padding: 8px 10px; border: 1px solid var(--brand-border); border-radius: 6px; font-size: 14px; box-sizing: border-box;">
            </div>
            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--brand-muted);">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button type="submit"
                style="margin-top: 8px; padding: 10px; border: none; border-radius: 6px; background: var(--brand-indigo); color: #fff; font-size: 14px; cursor: pointer;">
                Log in
            </button>
        </form>
    </div>
</body>
</html>
