<?php 

namespace Base;

use Exception;
use ReflectionClass;


class Module
{
	private static $defaultModuleName = NULL;
	private static $moduleClassNames = array(); // key: module name, value: fully qualified module class name
	
	public static function SetDefault($defaultModuleName)
	{
		self::$defaultModuleName = strtolower($defaultModuleName);
	}
	
	public static function Register($moduleName, $moduleClassName)
	{
		self::$moduleClassNames[strtolower($moduleName)] = $moduleClassName;
	}
	
	private static function GetClassName($inModuleName)
	{
		return isset(self::$moduleClassNames[$inModuleName]) ? self::$moduleClassNames[$inModuleName] : NULL;
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
		
		if (!isset($sModuleInstances[$key]))
		{
			$className = self::GetClassName($key);
			if (is_string($className))
			{
				$sModuleInstances[$key] = new $className();
			}
			else
			{
				throw new Exception('Unknown module: '. $inName);
			}
			
		}
		
		return $sModuleInstances[$key];
	}
	
	public static function Start()
	{
		// load module
		$moduleName = GetArg('module', self::$defaultModuleName);
		
		if ($moduleName)
		{
			$module =& Module::Get($moduleName);
			$module->run(GetArg('action', 'default'));
			exit;
		}

		throw new Exception('No module defined!');
	}
	
	
	protected $name;
	protected $viewPaths;
	
	protected function __construct($inName)
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
	protected function getViewPath($inViewName)
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
	
	protected function createView($inViewName)
	{
		$result = new View($this->getViewPath($inViewName));
		return $result;
		
	}
	
}
