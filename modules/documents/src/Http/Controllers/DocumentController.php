<?php

namespace Moduloom\Documents\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Moduloom\Documents\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        return Document::query()->latest()->get()->map(fn (Document $doc) => [
            ...$doc->toArray(),
            'url' => $doc->url(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'max:10240']]);

        $upload = $request->file('file');
        $path = $upload->store('documents', 'public');

        $document = Document::create([
            'title' => $request->input('title', $upload->getClientOriginalName()),
            'disk_path' => $path,
            'mime_type' => $upload->getClientMimeType(),
            'size_bytes' => $upload->getSize(),
        ]);

        return response()->json([...$document->toArray(), 'url' => $document->url()], 201);
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->disk_path);
        $document->delete();

        return response()->noContent();
    }
}
