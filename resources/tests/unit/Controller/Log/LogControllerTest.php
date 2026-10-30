<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;

class LogControllerTest extends TestCase
{
    private string $emptyLogDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyLogDir = sys_get_temp_dir().'/hcms-empty-logs-'.uniqid();

        if (! is_dir($this->emptyLogDir)) {
            mkdir($this->emptyLogDir, 0777, true);
        }

        Config::set('logging.default', 'daily');
        Config::set('logging.channels.daily.path', $this->emptyLogDir.'/laravel.log');
    }

    protected function tearDown(): void
    {
        if (is_dir($this->emptyLogDir)) {
            array_map('unlink', glob($this->emptyLogDir.'/*') ?: []);
            rmdir($this->emptyLogDir);
        }

        parent::tearDown();
    }

    public function testIndexWithoutLogFilesDoesNotRequireACurrentFile(): void
    {
        $view = (new \App\Controllers\LogController)->index();

        $data = $view->getData();

        $this->assertNull($data['current_file']);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data['entries']);
        $this->assertSame(0, $data['entries']->total());
    }
}
