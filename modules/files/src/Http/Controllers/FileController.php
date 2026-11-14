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
        // Extension/content allowlist, not just a size cap. Unrestricted
        // uploads are a real path to stored XSS (an uploaded .html file
        // served back verbatim) or worse on shared hosting where PHP
        // execution can't always be disabled per-directory — Laravel's
        // `mimes` rule sniffs real file content, not just the
        // client-supplied extension, so a renamed .php can't pass as
        // .pdf.
        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,txt,csv,zip'],
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
