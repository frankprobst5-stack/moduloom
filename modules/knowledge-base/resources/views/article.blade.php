<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $article->title }}</title>
</head>
<body>
    <h1>{{ $article->title }}</h1>
    <div>{!! nl2br(e($article->content)) !!}</div>
</body>
</html>
