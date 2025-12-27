<?php 

namespace App\Http;

/**
 * @deprecated deprecated since version 1.0.0
 */
class RouteResolver {

	private $defaultNamespace = "\App\\Controllers\\";

	private $namespace;

	public function __construct(){
		$this->resetNamespace();
	}

	public function getNamespace(): string {
		return $this->namespace;
	}

	public function resetNamespace(): void {
		$this->namespace = $this->defaultNamespace;
	}

	public function changeNamespace(string $namespace): void {
		$this->namespace = $namespace;
	}

	public function resolveControllerClass(string $controller): string {

		$controller_name = studly_case($controller).'Controller';

		$controllerClass = $this->namespace.$controller_name;


		if(!class_exists($controllerClass)){

			throw new \App\Exceptions\FileNotFoundException('<b>'.$controllerClass.'.php'.'</b>');
		} 

		return $controllerClass;
	}


	public function resolve(string $controller = 'dashboard', string | null $action = null, string | null | int $args = null){

				$action!="" || $action===null ? : $action='index';		

		        $controllerClass = \App::make($this->resolveControllerClass($controller));

		        $action = studly_case($action);


		      	if(method_exists($controllerClass, 'before')){
		            $controllerClass->callAction('before', [$args]);
		        }


		        if(method_exists($controllerClass, $action)){
		          
		            return $controllerClass->callAction($action, [$args]);
		        }
		        else{
		            throw new \BadMethodCallException("Couldn't find action: <b>".lcfirst($action)."</b>");
		        }

	}











}