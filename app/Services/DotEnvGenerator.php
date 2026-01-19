<?php 

namespace App\Services;


class DotEnvGenerator {

	private array $content = [];
	private string $path = "";
	private string $file = ".env";

	public function __construct(array|null $envs) {
		if(is_null($envs)){
			return;
		}

		$this->addEnvVars($envs);
	}

	public function setPath(string $path): void {
		$this->path = $path."/";
	}

	public function addEnvVars(array $envs): void {
		foreach($envs as $key => $val){
			$this->addEnvVar($key, $val);
		}
	}

	public function addEnvVar(string $var, string $val): void {
		$this->content[strtoupper($var)]=$val; 
	}

	public function getEnvVars(): array {
		return $this->content;
	}

	public function generate(): bool {

		$file_content = "";

		foreach($this->content as $key => $val){
			$file_content .= strtoupper($key)."=".$val.PHP_EOL;
		}

		return file_put_contents($this->path.$this->file,$file_content);
	}



}