<?php

namespace Base;

use PDO;


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
			if (!is_array($result))
			{
				$this->preparedStatement = NULL;
				$result = NULL;
			}
		}
		
		return $result;
	}
	
	public function getResults($inKeyField = NULL, $inLimit = 10000)
	{
		$limit = $inLimit;
		$results = NULL;
		
		if ($this->preparedStatement !== NULL)
		{
			$results = array();
		
			$row = $this->nextRow();
			if ($row !== NULL)
			{
				if ($inKeyField)
				{
					while ($limit-- >= 0 && $row !== NULL)
					{
						$results[$row[$inKeyField]] = $row;
						$row = $this->nextRow();
					}
				}
				else
				{
					while ($limit-- >= 0 && $row !== NULL)
					{
						$results[] = $row;
						$row = $this->nextRow();
					}
				}
			}
						
			$this->preparedStatement = NULL;
		}
		
		return $results;
	}
}
