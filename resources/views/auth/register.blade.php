<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/brand/moduloom-mark.svg">
    <title>Create the owner account — Moduloom</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div style="max-width: 360px; margin: 80px auto; font-family: var(--brand-font-body);">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 28px;">
            <img src="/brand/moduloom-mark.svg" width="28" height="28" alt="">
            <span style="font-family: var(--brand-font-display); font-weight: 600; font-size: 22px; color: var(--brand-charcoal);">Moduloom</span>
        </div>

        <h1 style="font-family: var(--brand-font-display); font-weight: 600; font-size: 22px; margin-bottom: 4px;">Create the owner account</h1>
        <p style="color: var(--brand-muted); font-size: 13.5px; margin-top: 0 0 20px; line-height: 1.5;">
            This instance has no account yet. The first one created here becomes its owner — this screen closes itself the moment it exists.
        </p>

        <form method="POST" action="/register" style="display: flex; flex-direction: column; gap: 14px;">
            @csrf
            <div>
                <label for="name" style="display: block; font-size: 13px; color: var(--brand-muted); margin-bottom: 4px;">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    style="width: 100%; padding: 8px 10px; border: 1px solid var(--brand-border); border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                @error('name')
                    <p style="color: var(--brand-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" style="display: block; font-size: 13px; color: var(--brand-muted); margin-bottom: 4px;">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    style="width: 100%; padding: 8px 10px; border: 1px solid var(--brand-border); border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                @error('email')
                    <p style="color: var(--brand-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" style="display: block; font-size: 13px; color: var(--brand-muted); margin-bottom: 4px;">Password</label>
                <input id="password" name="password" type="password" required minlength="8"
                    style="width: 100%; padding: 8px 10px; border: 1px solid var(--brand-border); border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                @error('password')
                    <p style="color: var(--brand-danger); font-size: 12px; margin: 4px 0 0;">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                style="margin-top: 8px; padding: 10px; border: none; border-radius: 6px; background: var(--brand-indigo); color: #fff; font-size: 14px; cursor: pointer;">
                Create account
            </button>
        </form>
    </div>
</body>
</html>
