<h1>From other instances</h1>

@forelse ($itemsByType as $contentType => $items)
    <h2>{{ ucwords(str_replace('-', ' ', $contentType)) }}</h2>
    @foreach ($items as $item)
        <article>
            <h3>{{ $item->title }}</h3>
            @if ($item->content)
                <p>{{ \Illuminate\Support\Str::limit($item->content, 280) }}</p>
            @endif
            <p><small>From {{ $item->peer->label }} &mdash; <a href="{{ $item->canonical_url }}">view on the original instance</a></small></p>
        </article>
    @endforeach
@empty
    <p>No federated content yet — subscribe to a peer instance from the dashboard to see its public content here.</p>
@endforelse
