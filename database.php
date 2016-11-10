<?php

class ResultSet
{
	private $preparedStatement;
	
	public function __construct($inPreparedStatement)
	{
		$this->preparedStatement = $inPreparedStatement;
	}
	
	public function nextRow()
	{
		$result = NULL;
		
		if ($this->preparedStatement !== NULL)
		{
			$result = $this->preparedStatement->fetch(PDO::FETCH_ASSOC);
			if ($result == NULL)
			{
				$this->preparedStatement = NULL;
			}
		}
		
		return $result;
	}
	
	public function getResults($inKeyField = NULL, $inLimit = 10000)
	{
		$limit = $inLimit;
		$results = array();
		
		if ($inKeyField)
		{
			while ($limit-- >= 0 && $row = $this->nextRow())
			{
				$results[$row[$inKeyField]] = $row;
			}
		}
		else
		{
			while ($limit-- >= 0 && $row = $this->nextRow())
			{
				$results[] = $row;
			}
		}
		
		$this->preparedStatement = NULL;
		
		return $results;
	}
}


class Database
{
	private static $instances = array();
	
	public static function & Get($inDatabase = 'main')
	{
		if (!isset(self::$instances[$inDatabase]))
		{
			try
			{
				self::$instances[$inDatabase] = new self(self::GetConfig($inDatabase));
			}
			catch (Exception $e)
			{
				self::$instances[$inDatabase] = false;
			}
		}
		
		if (self::$instances[$inDatabase] === false)
		{
			throw new Exception('db_not_connected');
		}
		
		return self::$instances[$inDatabase];
	}
	
	private static function GetConfig($inDatabase)
	{
		$config = isset($GLOBALS['config']['databases'][$inDatabase]) ? $GLOBALS['config']['databases'][$inDatabase] : NULL;
		if (!is_array($config))
		{
			throw new Exception('db_not_configured');
		}
		
		return $config;
	}
	
	
	private $connection = NULL;

	private function __construct($config)
	{
		try
		{
			$this->connection = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config['host'], $config['database']), $config['user'], $config['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		}
		catch (PDOException $e)
		{
			$this->connection = false;
			throw new Exception('db_connection_failed: ' . $e);
		}
	}
	
	public function executeQuery($inQuery, $inArguments = NULL)
	{
		return new ResultSet($this->execute($inQuery, $inArguments));
	}
	
	public function executeUpdate($inStatement, $inArguments = NULL)
	{
		return $this->execute($inStatement, $inArguments)->rowCount();
	}
	
	public function lastInsertId()
	{
		return $this->connection->lastInsertId();
	}
	
	private function execute($inSQL, $inArguments)
	{
		// prepare
		$preparedStatement = $this->connection->prepare($inSQL);
		
		$arguments = array();
		foreach ($inArguments as $key => $value)
		{
			if (!isset($this->escapeArguments[$key]) || $this->escapeArguments[$key])
			{
				$arguments[sprintf(':%s', $key)] = $value;
			}
		}
		
		$executed = $preparedStatement->execute($inArguments);
		if (!$executed)
		{
			throw new Exception(); // TODO: improve exceptions
		}
		
		return $preparedStatement;
	}
}
