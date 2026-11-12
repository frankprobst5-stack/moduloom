<?php

namespace Moduloom\Files\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Moduloom\Files\Models\UploadedFile;

class FileController extends Controller
{
    public function index()
    {
        return UploadedFile::query()->latest()->get()->map(fn (UploadedFile $file) => [
            ...$file->toArray(),
            'url' => $file->url(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $upload = $request->file('file');
        $path = $upload->store('files', 'public');

        $record = UploadedFile::create([
            'original_name' => $upload->getClientOriginalName(),
            'disk_path' => $path,
            'mime_type' => $upload->getClientMimeType(),
            'size_bytes' => $upload->getSize(),
        ]);

        return response()->json([...$record->toArray(), 'url' => $record->url()], 201);
    }

    public function destroy(UploadedFile $uploadedFile)
    {
        Storage::disk('public')->delete($uploadedFile->disk_path);
        $uploadedFile->delete();

        return response()->noContent();
    }
}
