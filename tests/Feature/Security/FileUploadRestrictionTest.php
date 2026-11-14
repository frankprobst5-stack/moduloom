<?php

namespace Tests\Feature\Security;

use App\Kernel\ModuleInstaller;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Security review (Phase 5): uploads must be restricted to a safe
 * extension/content allowlist, not just a size cap. An unrestricted
 * upload is a real path to stored XSS (an uploaded .html file served
 * back verbatim) or worse on shared hosting, where PHP execution
 * can't always be disabled per-directory.
 */
class FileUploadRestrictionTest extends TestCase
{
    public function test_files_module_rejects_a_php_upload(): void
    {
        Storage::fake('public');
        app(ModuleInstaller::class)->install('files', 'manual', 'owner');

        $this->post('/api/modules/files/files', [
            'file' => UploadedFile::fake()->create('shell.php', 1),
        ])->assertStatus(422);
    }

    public function test_files_module_rejects_an_html_upload(): void
    {
        Storage::fake('public');
        app(ModuleInstaller::class)->install('files', 'manual', 'owner');

        $this->post('/api/modules/files/files', [
            'file' => UploadedFile::fake()->create('page.html', 1),
        ])->assertStatus(422);
    }

    public function test_files_module_still_accepts_a_legitimate_pdf(): void
    {
        Storage::fake('public');
        app(ModuleInstaller::class)->install('files', 'manual', 'owner');

        $this->post('/api/modules/files/files', [
            'file' => UploadedFile::fake()->create('receipt.pdf', 10),
        ])->assertCreated();
    }

    public function test_documents_module_rejects_a_php_upload(): void
    {
        Storage::fake('public');
        app(ModuleInstaller::class)->install('documents', 'manual', 'owner');

        $this->post('/api/modules/documents/documents', [
            'file' => UploadedFile::fake()->create('shell.php', 1),
        ])->assertStatus(422);
    }
}
