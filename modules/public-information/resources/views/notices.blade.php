<h1>Public Notices</h1>
@foreach ($notices as $notice)
    <article>
        <h2>{{ $notice->title }}</h2>
        <p>{{ $notice->body }}</p>
    </article>
@endforeach

@if ($federated->isNotEmpty())
    <h2>From other instances</h2>
    @foreach ($federated as $item)
        <article>
            <h3>{{ $item->title }}</h3>
            <p>{{ $item->content }}</p>
            <p><small>From {{ $item->peer->label }} &mdash; <a href="{{ $item->canonical_url }}">view on the original instance</a></small></p>
        </article>
    @endforeach
@endif
