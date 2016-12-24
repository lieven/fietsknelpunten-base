<?php 

namespace Base;

use Exception;
use ReflectionClass;


class Module
{
	protected $name;
	protected $viewPaths;
	
	public function __construct($inName)
	{
		$this->name = $inName;
		$this->viewPaths = array();
	}
	
	// run an action
	public function run($inAction)
	{
		if (! preg_match('/^([a-zA-Z][a-zA-Z0-9_]*)$/', $inAction))
		{
			throw new Exception('Invalid action name: '. $inAction);
		}
		
		$actionMethod = $inAction .'Action';
		if (! method_exists($this, $actionMethod))
		{
			throw new Exception('Unknown action: '. $inAction .' for module '. get_class($this));
		}
		
		$this->$actionMethod();
	}
	
	// overload standard view paths for this module
	protected function setViewPath($inViewName, $inViewPath)
	{
		if (! preg_match('/^([a-zA-Z][a-zA-Z0-9_]*)$/', $inViewName))
		{
			throw new Exception('Invalid view name: '. $inViewName);
		}
		
		$this->viewPaths[$inViewName] = $inViewPath;
	}
	
	// get path for a view
	public function getViewPath($inViewName)
	{
		if (! preg_match('/^([a-zA-Z][a-zA-Z0-9_]*)$/', $inViewName))
		{
			throw new Exception('Invalid view name: '. $inViewName);
		}
		
		if (!isset($this->viewPaths[$inViewName]))
		{
			$reflector = new ReflectionClass(get_class($this));
			$pathInfo = pathinfo($reflector->getFileName());
			
			$viewPath = $pathInfo['dirname'] . '/'. $pathInfo['filename'] . '.' . $inViewName . '.php';
			
			$this->viewPaths[$inViewName] = $viewPath;
		}
		
		return $this->viewPaths[$inViewName];
	}
	
	public function createView($inViewName)
	{
		$result = new View($this->getViewPath($inViewName));
		$result->setArg('resourceFolder', ResourcePath($this->name));
		return $result;
		
	}
	
	public static function GetClassName($inModuleName)
	{
		$className = Config::Get('modules', 'override', $inModuleName);
		if (!is_string($className))
		{
			$namespace = Config::Get('modules', 'namespace');
			if (!is_string($namespace))
			{
				$namespace = '';
			}
			
			$className = $namespace . '\\' . ucfirst(strtolower($inModuleName)) . 'Module';
		}
		
		return $className;
	}
	
	// Get an instance of this module
	public static function & Get($inName)
	{
		static $sModuleInstances = array();
		
		if (! preg_match('/^([a-zA-Z][a-zA-Z0-9_]*)$/', $inName))
		{
			throw new Exception('Invalid module name: '. $inName);
		}
		
		$key = strtolower($inName);
		
		if (! isset($sModuleInstances[$key]))
		{
			$className = self::GetClassName($inName);
			
			$sModuleInstances[$key] = new $className();
		}
		
		return $sModuleInstances[$key];
	}
	
	public static function RunDefault()
	{
		// load module
		$moduleName = GetArg('module', Config::Get('modules', 'default'));

		if ($moduleName)
		{
			$module =& Module::Get($moduleName);
			$module->run(GetArg('action', 'default'));
			exit;
		}

		throw new Exception('No module defined!');
	}
}
