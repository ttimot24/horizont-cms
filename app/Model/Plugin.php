<?php

namespace App\Model;

use App\Model\Trait\HasImage;
use App\Model\Trait\IsActive;
use \Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\BooleanNot;

class Plugin extends Model
{

	use HasImage;
	use IsActive;

	protected $fillable = ['id', 'root_dir', 'version', 'area', 'permission', 'table_name', 'active'];

	private $info = null;

	protected $imageDir;

	public function __construct($root_dir = null)
	{
		$this->image = "icon.jpg";
		$this->defaultImage = "resources/images/icons/plugin.png";

		if (isset($root_dir) && !is_array($root_dir)) {

			// FIXME Use first
			$eloquent = self::rootDir($root_dir)->first();

			if (isset($eloquent)) {
				parent::__construct($eloquent->attributes);
			}

			isset($this->root_dir) ?: $this->setRootDir($root_dir);

			$this->imageDir = $this->getPath();
		}
	}

	public function scopeRootDir($query, $root_dir)
	{
		return $query->where('root_dir', $root_dir);
	}

	public function scopeActive($query)
	{
		return $query->where('active', '1');
	}

	public function setRootDir($root_dir): void
	{
		$this->root_dir = $root_dir;
	}

	public function exists(): bool
	{
		return file_exists($this->getPath());
	}

	public function isInstalled(): bool
	{
		$result = self::rootDir($this->root_dir)->get();

		return !$result->isEmpty();
	}

	public function isActive(): bool
	{

		return ($this->isInstalled() && $this->active == 1);
	}

	public function getConfig($config, $default = null)
	{

		isset($this->config) ?: $this->config = file_exists($this->getPath() . "/config.php") ? require($this->getPath() . "/config.php") : null;

		return isset($this->config[$config]) ? $this->config[$config] : $default;
	}

	public function getName(): string
	{
		return $this->getInfo('name') == null ? $this->root_dir : $this->getInfo('name');
	}

	public function getNamespaceFor($for): string
	{
		return "\Plugin\\" . $this->root_dir . "\\App\\" . ucfirst($for) . "\\";
	}

	public function getSlug(): string
	{
		return namespace_to_slug($this->root_dir);
	}

	public function getPath(): string
	{
		return 'plugins' . DIRECTORY_SEPARATOR . $this->root_dir;
	}

	public function getDatabaseFilesPath(): string | null
	{

		$path_to_db = $this->getPath() . '/database';

		if (file_exists($path_to_db) && is_dir($path_to_db)) {
			return $path_to_db;
		}

		return null;
	}

	private function loadInfoFromFile(): void
	{

		$file_without_extension = $this->getPath() . "/plugin_info";

		if (file_exists($file_without_extension . ".yml") && class_exists('\Symfony\Component\Yaml\Yaml')) {
			$this->setAllInfo((object) \Symfony\Component\Yaml\Yaml::parse(
				file_get_contents($file_without_extension . ".yml"),
				\Symfony\Component\Yaml\Yaml::PARSE_OBJECT
			));
		} else if (file_exists($file_without_extension . ".json")) {
			$this->setAllInfo(json_decode(file_get_contents($file_without_extension . ".json")));
		} else if (file_exists($file_without_extension . ".xml")) {
			$this->setAllInfo(simplexml_load_file($file_without_extension . ".xml"));
		}
	}

	public function hasInfo(): bool
	{
		return isset($this->info);
	}

	public function setAllInfo($all_info): void
	{
		$this->info = $all_info;
	}

	public function getInfo($info)
	{

		if (!$this->hasInfo()) {
			$this->loadInfoFromFile();
		}

		return isset($this->info->{$info}) ? $this->info->{$info} : null;
	}

	public function getShortCode(): string
	{
		return "{[" . $this->root_dir . "]}";
	}


	public function hasRegisterClass(): bool
	{
		return class_exists($this->getRegisterClass());
	}

	public function getRegisterClass(): string
	{
		return "\Plugin\\" . $this->root_dir . "\Register";
	}

	public function hasRegister($register): bool
	{
		return method_exists($this->getRegisterClass(), $register);
	}


	public function getRegister($register, $default = null)
	{

		$plugin_namespace = $this->getRegisterClass();

		if ($this->hasRegister($register)) {

			$instance = new $plugin_namespace();

			if ($instance instanceof \App\Interfaces\PluginInterface) {
				return $instance->$register();
			}
		}

		return $default;
	}

	public function isUpdatable(): bool
	{

		if($this->isInstalled()
		   && $this->isCompatibleWithCore()
		   && \Composer\Semver\Comparator::lessThan(ltrim(empty($this->version)? "0.0" : $this->version, 'v'), ltrim($this->getInfo('version'),'v')) 
		){
			return true;
		}
		
		return false;
	}

	public function getRequirements()
	{
		return $this->getInfo('requires');
	}

	public function getRequiredCoreVersion()
	{
		return ltrim(empty($this->getInfo('requires')->core)? 'v0.0.0' : $this->getInfo('requires')->core, 'v');
	}

	public function isCompatibleWithCore(): bool
	{
		return \Composer\Semver\Comparator::greaterThanOrEqualTo(ltrim(config('horizontcms.version'), 'v'), $this->getRequiredCoreVersion());
	}
}
