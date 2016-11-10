<?php // Module base class

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
			throw new Exception('Invalid action name: '. $inAction);
		
		$actionMethod = $inAction .'Action';
		if (! method_exists($this, $actionMethod))
			throw new Exception('Unknown action: '. $inAction .' for module '. get_class($this));
		
		$this->$actionMethod();
	}
	
	// overload standard view paths for this module
	protected function setViewPath($inViewName, $inViewPath)
	{
		$this->viewPaths[$inViewName] = $inViewPath;
	}
	
	// get path for a view
	public function getViewPath($inViewName)
	{
		return isset($this->viewPaths[$inViewName])
		          ? $this->viewPaths[$inViewName]
		          : sprintf('views/%s.%s.php', $this->name, $inViewName);
	}
	
	// Get an instance of this module
	public static function & Get($inName)
	{
		static $sModuleInstances = array();
		
		$key = strtolower($inName);
		
		if (! isset($sModuleInstances[$key]))
		{
			$className = ucfirst($inName) . 'Module';
			
			$sModuleInstances[$key] = new $className();
		}
		
		return $sModuleInstances[$key];
	}
}