<h1>Public Notices</h1>
@foreach ($notices as $notice)
    <article>
        <h2>{{ $notice->title }}</h2>
        <p>{{ $notice->body }}</p>
    </article>
@endforeach
