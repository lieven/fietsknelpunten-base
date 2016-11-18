<?php

namespace Base;

use \Exception;
use \PDO;


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
		$config = Config::Get('databases', $inDatabase);
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
		
		$executed = $preparedStatement->execute($inArguments);
		if (!$executed)
		{
			throw new Exception(); // TODO: improve exceptions
		}
		
		return $preparedStatement;
	}
}
