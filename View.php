<?php // View

namespace Base;

use Exception;


function RecursiveHtmlSpecialChars($inValue)
{
	if (is_array($inValue))
	{
		$result = array();
		
		foreach ($inValue as $key => $value)
		{
			$result[$key] = RecursiveHtmlSpecialChars($value);
		}
		
		return $result;
	}
	else
	{
		return htmlspecialchars($inValue);
	}
}


class View
{
	private $args = array();
	private $viewIncludePath;
	private $wrapperView = NULL;
	
	public function __construct($inViewPath)
	{
		if (!stream_resolve_include_path($inViewPath))
		{
			throw new Exception('View path not found: ' . $inViewPath);
		}
		
		$this->viewIncludePath = $inViewPath;
	}
	
	public function show()
	{
		foreach (array_keys($this->args) as $_key_)
		{
			$$_key_ =& $this->args[$_key_];
		}
		
		include $this->viewIncludePath;
		
		if ($this->wrapperView)
		{
			$this->wrapperView->body = ob_get_clean();
			$this->wrapperView->show();
			$this->wrapperView = NULL;
		}
	}
	
	public function setWrapper($inWrapperViewPath, $inTitle)
	{
		$contentStart = $this->wrapperView ? ob_get_clean() : NULL;
		
		$this->wrapperView = new View($inWrapperViewPath);
		$this->wrapperView->title = $inTitle;
		
		ob_start();
		
		if ($contentStart !== NULL)
		{
			echo $contentStart;
		}
	}
	
	public function setArg($inName, $inValue, $inHtmlSpecialChars = true)
	{
		if ($inHtmlSpecialChars)
		{
			$this->args[$inName] = RecursiveHtmlSpecialChars($inValue);
		}
		else
		{
			$this->args[$inName] = $inValue;
		}
	}
	
	public function __set($inName, $inValue)
	{
		$this->args[$inName] = $inValue;
	}
	
	public function __get($inName)
	{
		if (isset($this->args[$inName]))
		{
			return $this->args[$inName];
		}
		return NULL;
	}
	
	public function __isset($inName)
	{
		return isset($this->args[$inName]);
	}
	
	public function __unset($inName)
	{
		unset($this->args[$inName]);
	}
	
	public function __tostring()
	{
		ob_start();
		$this->show();
		return ob_get_clean();
	}
}
