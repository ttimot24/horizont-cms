<?php

use App\Services\PluginRegistry;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PluginRegistryTest extends TestCase
{
    /**
     * Throw away plugin directory name used by the download tests.
     */
    private const TEST_PLUGIN = '__plugin_registry_test__';

    private const STORE_URL = 'https://store.test';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'horizontcms.hcms_store_url' => self::STORE_URL,
            'horizontcms.hcms_store_token' => null,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('plugins/'.self::TEST_PLUGIN));
        File::delete(storage_path('framework/temp/'.self::TEST_PLUGIN.'.zip'));

        parent::tearDown();
    }

    public function testApiUrlIsDerivedFromTheConfiguredStoreUrl(): void
    {
        $this->assertSame(
            self::STORE_URL.'/api/v1/plugins',
            (new PluginRegistry)->getApiUrl()
        );
    }

    public function testClientSendsTheConfiguredTokenAsBearerToken(): void
    {
        config(['horizontcms.hcms_store_token' => 'secret-token']);

        Http::fake(['*' => Http::response([])]);

        (new PluginRegistry)->client()->get(self::STORE_URL.'/api/v1/plugins');

        Http::assertSent(
            fn (ClientRequest $request) => $request->hasHeader('Authorization', 'Bearer secret-token')
        );
    }

    public function testClientDoesNotSendAnAuthorizationHeaderWithoutAToken(): void
    {
        Http::fake(['*' => Http::response([])]);

        (new PluginRegistry)->client()->get(self::STORE_URL.'/api/v1/plugins');

        Http::assertSent(fn (ClientRequest $request) => ! $request->hasHeader('Authorization'));
    }

    public function testFetchPluginsRequestsTheApiUrlAndAcceptsAnEmptyStore(): void
    {
        Http::fake(['*' => Http::response([])]);

        $plugins = (new PluginRegistry)->fetchPlugins();

        $this->assertInstanceOf(Collection::class, $plugins);
        $this->assertTrue($plugins->isEmpty());

        Http::assertSent(
            fn (ClientRequest $request) => $request->method() === 'GET'
                && $request->url() === self::STORE_URL.'/api/v1/plugins'
        );
    }

    public function testDownloadPluginReturnsFalseWhenTheStoreRejectsTheRequest(): void
    {
        Http::fake(['*' => Http::response('Plugin not found', 404)]);

        $this->assertFalse((new PluginRegistry)->downloadPlugin(self::TEST_PLUGIN));

        Http::assertSent(
            fn (ClientRequest $request) => $request->url() === self::STORE_URL.'/api/v1/plugins/'.self::TEST_PLUGIN.'/download'
        );

        $this->assertDirectoryDoesNotExist(base_path('plugins/'.self::TEST_PLUGIN));
    }

    public function testDownloadPluginExtractsAnArchiveThatContainsThePluginFolder(): void
    {
        Http::fake(['*' => Http::response($this->pluginArchive(self::TEST_PLUGIN, true))]);

        $this->assertTrue((new PluginRegistry)->downloadPlugin(self::TEST_PLUGIN));

        $this->assertFileExists(base_path('plugins/'.self::TEST_PLUGIN.'/plugin_info.xml'));
        $this->assertFileExists(base_path('plugins/'.self::TEST_PLUGIN.'/Register.php'));
    }

    public function testDownloadPluginExtractsAnArchiveWithoutThePluginFolder(): void
    {
        Http::fake(['*' => Http::response($this->pluginArchive(self::TEST_PLUGIN, false))]);

        $this->assertTrue((new PluginRegistry)->downloadPlugin(self::TEST_PLUGIN));

        $this->assertFileExists(base_path('plugins/'.self::TEST_PLUGIN.'/plugin_info.xml'));
        $this->assertFileExists(base_path('plugins/'.self::TEST_PLUGIN.'/Register.php'));
    }

    public function testDownloadPluginRemovesTheTemporaryArchive(): void
    {
        Http::fake(['*' => Http::response($this->pluginArchive(self::TEST_PLUGIN, true))]);

        (new PluginRegistry)->downloadPlugin(self::TEST_PLUGIN);

        $this->assertFileDoesNotExist(storage_path('framework/temp/'.self::TEST_PLUGIN.'.zip'));
    }

    /**
     * Build the body of a plugin archive, optionally wrapped in a base folder.
     */
    private function pluginArchive(string $plugin, bool $withBaseFolder): string
    {
        $path = storage_path('framework/temp/'.$plugin.'.source.zip');

        File::ensureDirectoryExists(dirname($path));
        File::delete($path);

        $archive = new ZipArchive;
        $archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $prefix = $withBaseFolder ? $plugin.'/' : '';

        $archive->addFromString($prefix.'plugin_info.xml', '<?xml version="1.0"?><plugin><name>Test</name></plugin>');
        $archive->addFromString($prefix.'Register.php', "<?php\n");

        $archive->close();

        $body = File::get($path);

        File::delete($path);

        return $body;
    }
}
