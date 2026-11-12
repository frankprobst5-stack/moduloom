<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Public Notices</title>
</head>
<body>
    <h1>Public Notices</h1>
    @foreach ($notices as $notice)
        <article>
            <h2>{{ $notice->title }}</h2>
            <p>{{ $notice->body }}</p>
        </article>
    @endforeach
</body>
</html>
