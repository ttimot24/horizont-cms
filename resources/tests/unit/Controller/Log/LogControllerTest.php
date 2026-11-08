<?php

use App\Controllers\LogController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Opcodes\LogViewer\Facades\LogViewer;

class LogControllerTest extends TestCase
{
    public function test_index_renders_the_cms_log_view_with_no_files(): void
    {
        Config::set('log-viewer.include_files', []);

        $view = (new LogController)->index();
        $data = $view->getData();

        $this->assertSame('settings.log', $view->getName());
        $this->assertNull($data['current_file']);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data['entries']);
        $this->assertSame(0, $data['entries']->total());
    }

    public function test_index_includes_the_full_exception_context(): void
    {
        $logPath = storage_path('logs/laravel-test-'.Str::uuid().'.log');
        $previousFiles = Config::get('log-viewer.include_files');
        $stackTrace = "[object] (RuntimeException(code: 0): Request failed)\n[stacktrace]\n#0 /app/example.php(10): run()\n#1 {main}";
        $logLine = '[2025-01-01 10:00:00] testing.ERROR: Request failed '.json_encode(['exception' => $stackTrace]);

        File::ensureDirectoryExists(dirname($logPath));
        file_put_contents($logPath, $logLine);
        Config::set('log-viewer.include_files', [$logPath]);
        LogViewer::clearFileCache();

        try {
            $view = (new LogController)->index();
            $entry = $view->getData()['entries']->first();

            $this->assertStringContainsString('#0 /app/example.php(10): run()', $entry->context);
            $this->assertStringContainsString('#1 {main}', $entry->context);
        } finally {
            unlink($logPath);
            Config::set('log-viewer.include_files', $previousFiles);
            LogViewer::clearFileCache();
        }
    }
}
