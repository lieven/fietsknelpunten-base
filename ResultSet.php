<?php

namespace Base;

use \PDO;


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
