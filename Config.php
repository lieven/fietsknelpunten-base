<?php

namespace Base;

use \Exception;


class Config
{
	private static $config = NULL;

	static function Register($inConfiguration)
	{
		if (self::$config !== NULL)
		{
			throw new Exception('Cannot override config!');
		}
		
		self::$config = $inConfiguration;
	}
	
	static function Load()
	{
		if (get_magic_quotes_gpc())
		{
			throw new Exception('Please disable magic quotes');
		}
		
		if (!defined('Base\CONFIG_FILE'))
		{
			throw new Exception('Base\CONFIG_FILE not defined');
		}
		
		if (!stream_resolve_include_path(\Base\CONFIG_FILE))
		{
			throw new Exception('Config file not found');
		}
		
		require_once(\Base\CONFIG_FILE);
		
		if (self::$config === NULL)
		{
			throw new Exception('Config not registered');
		}
	}
	
	static function Get(/* $key1, $key2,..., $keyN */)
	{
		$result = NULL;
	
		if (self::$config !== NULL)
		{
			$result = self::$config;
		
			for ($i = 0, $n = func_num_args(); $i < $n; ++$i)
			{
				$key = func_get_arg($i);
				if (!is_string($key) || !isset($result[$key]))
				{
					$result = NULL;
					break;
				}
			
				$result = $result[$key];
			
				if ($i + 1 < $n && !is_array($result))
				{
					$result = NULL;
					break;
				}
			}
		}
	
		return $result;
	}
}