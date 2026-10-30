<?php

use App\Model\Plugin;
use App\Services\PluginRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * The registry looks up the downloaded plugin through the Plugin model, which
 * queries the plugins table, so this case needs a database.
 */
class PluginRegistryFetchTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_PLUGIN = '__plugin_registry_test__';

    protected function setUp(): void
    {
        if (! in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The sqlite PDO driver is required to query the plugins table.');
        }

        parent::setUp();

        config([
            'horizontcms.hcms_store_url' => 'https://store.test',
            'horizontcms.hcms_store_token' => null,
        ]);

        File::ensureDirectoryExists(base_path('plugins/'.self::TEST_PLUGIN));
    }

    protected function tearDown(): void
    {
        // The application is not booted when the test is skipped in setUp().
        if ($this->app !== null) {
            File::deleteDirectory(base_path('plugins/'.self::TEST_PLUGIN));
        }

        parent::tearDown();
    }

    public function testFetchPluginsAttachesTheLocalModelOnlyWhenThePluginIsDownloaded(): void
    {
        Http::fake(['*' => Http::response([
            ['dir' => self::TEST_PLUGIN, 'info' => ['name' => 'Downloaded plugin']],
            ['dir' => 'missing-plugin', 'info' => ['name' => 'Remote plugin']],
        ])]);

        $plugins = (new PluginRegistry)->fetchPlugins();

        $this->assertCount(2, $plugins);

        $this->assertInstanceOf(Plugin::class, $plugins->first()->local);
        $this->assertNull($plugins->last()->local);

        $this->assertSame('Downloaded plugin', $plugins->first()->info->name);
        $this->assertSame('Remote plugin', $plugins->last()->info->name);
    }
}
