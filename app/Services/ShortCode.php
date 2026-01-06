<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ShortCode
{

	private array $widgets = [];

	public function initalize(Collection $plugins): void
	{

		foreach ($plugins as $plugin) {


			if ($plugin->hasRegister('widget')) {

				\View::addNamespace('plugin', [
					$plugin->getPath() . "/app/View",
					$plugin->getPath() . "/app/resources/views"
				]);

				$this->addWidget($plugin->getShortCode(), $plugin->getRegister('widget'));
			}
		}
	}

	public function addWidget(string $key, $value): void
	{
		$this->widgets[$key] = $value;
	}

	public function getWidget(string $key): mixed
	{
		return $this->widgets[$key];
	}

	public function getAll(): array
	{
		return $this->widgets;
	}

	public function compile(string|null $page): string
	{
		if (is_null($page)) {
			return "";
		}

		return count($this->widgets) === 0 ? $page : str_replace(array_keys($this->widgets), array_values($this->widgets), $page);
	}
}
