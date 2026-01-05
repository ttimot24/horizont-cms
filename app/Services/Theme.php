<?php

namespace App\Services;

use Illuminate\Support\Collection;

class Theme
{
	private string $root_dir;
	public $info;
	public string $languagePath = "resources/lang";

	public function __construct(string $root_dir)
	{
		$this->root_dir = $root_dir;
		//$this->info = file_exists($this->getPath()."theme_info.xml")? simplexml_load_file($this->getPath()."theme_info.xml") : NULL;

		$this->parseThemeInfo();

		$this->languagePath = config("theme:theme.language.path","resources/lang");

	}

	public function getRootDir(): string
	{
		return $this->root_dir;
	}

	public function templates(): array
	{

		if (file_exists('themes' . DIRECTORY_SEPARATOR . $this->root_dir . DIRECTORY_SEPARATOR . 'page_templates')) {
			return array_slice(scandir('themes' . DIRECTORY_SEPARATOR . $this->root_dir . DIRECTORY_SEPARATOR . 'page_templates'), 2);
		} else {
			new \Exception('Couldn\'t render the theme!');
		}

		return [];
	}

	public function parseThemeInfo(): void
	{

		$file_without_extension = $this->getPath() . "theme_info";

		if (file_exists($file_without_extension . ".yml") && class_exists('\Symfony\Component\Yaml\Yaml')) {
			$this->info = \Symfony\Component\Yaml\Yaml::parse(
				file_get_contents($file_without_extension . ".yml"),
				\Symfony\Component\Yaml\Yaml::PARSE_OBJECT
			);
		} else if (file_exists($file_without_extension . ".json")) {
			$this->info = json_decode(file_get_contents($file_without_extension . ".json"));
		} else if (file_exists($file_without_extension . ".xml")) {
			$this->info = simplexml_load_file($file_without_extension . ".xml");
		} else {
			$this->info = NULL;
		}
	}

	public function isCurrentTheme(): bool
	{
		return $this->root_dir == \Settings::get('theme');
	}

	public function getName(): string
	{
		return empty($this->getInfo('name'))? $this->root_dir : $this->getInfo('name');
	}


	public function getPath(): string
	{
		return 'themes/' . $this->root_dir . '/';
	}

	public function getSupportedLanguages(): Collection
	{
		$lang_dir = $this->getPath() . $this->languagePath;

		if (!file_exists($lang_dir)) {
			return collect();
		}

		return collect(array_slice(scandir($lang_dir), 2))->filter(function ($lang) {
			return substr_compare($lang, ".json", -strlen(".json")) === 0;
		})->map(function ($lang) {
			return str_replace('.json', '', $lang);
		});
	}

	public function getImage(): string
	{
		return $this->getPath() . "preview.jpg";
	}

	public function getInfo($info)
	{
		return isset($this->info->{$info}) ? $this->info->{$info} : NULL;
	}

	public function has404Template(): bool
	{
		return (file_exists($this->getPath() . "404.blade.php") || file_exists($this->getPath() . "404.php"));
	}

	public function hasWebsiteDownTemplate(): bool
	{
		return (file_exists($this->getPath() . "website_down.blade.php") || file_exists($this->getPath() . "website_down.php"));
	}

	public function getRequiredCoreVersion(): string
	{
		return ltrim(empty($this->getInfo('requires')->core)? 'v0.0.0' :  $this->getInfo('requires')->core, 'v');
	}

	public function isCompatibleWithCore(): bool
	{
		return \Composer\Semver\Comparator::greaterThanOrEqualTo(ltrim(config('horizontcms.version'), 'v'), $this->getRequiredCoreVersion());
	}
}
