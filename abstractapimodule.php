<?php

class ApiException extends Exception
{
	const UNKNOWN_ERROR = 1;
	const UNKNOWN_ACTION = 2;
	const PARAMETER_REQUIRED = 3;
	
	public function __construct($inErrorCode, $inMessage = '')
	{
		parent::__construct($inMessage, $inErrorCode);	
	}
}

class AbstractApiModule extends Module
{
	public function run($inAction)
	{
		try
		{
			$parameters = $this->getActionParameters($inAction);
			
			// Check action
			if (! is_array($parameters) || ! $this->checkPermissions($inAction))
			{
				throw new ApiException(ApiException::UNKNOWN_ACTION, 'Unknown action: ' . $inAction);
			}
			
			$actionMethod = $inAction .'Action';
			if (! method_exists($this, $actionMethod))
			{
				throw new ApiException(ApiException::UNKNOWN_ACTION, 'Unimplemented action: ' . $inAction);
			}
			
			// Check args
			$args = array();
			foreach ($parameters as $parameter)
			{
				$value = GetArg($parameter);
				if ($value === null)
				{
					throw new ApiException(ApiException::PARAMETER_REQUIRED, 'Parameter required: ' . $parameter);
				}
				$args[$parameter] = $value;
			}
			
			// Execute action method
			$this->$actionMethod($args);
		}
		catch (ApiException $e)
		{
			self::outputError($e);
		}
		catch (Exception $e)
		{
			self::outputError(new ApiException(ApiException::UNKNOWN_ERROR, $e->getMessage()));
		}
	}
	
	protected function getActionParameters($inAction)
	{
		throw new Exception('getActionParameters() not implemented');
	}
	
	protected function checkPermissions($inAction)
	{
		throw new Exception('checkPermissions() not implemented');
	}
	
	public static function outputError($inApiException)
	{
		self::outputJson(array('error' => $inApiException->getCode(), 'message' => $inApiException->getMessage()));
	}
	
	public static function outputJson($inValue)
	{
		//header('Content-Type: text/plain');
		header('Content-Type: application/json');
		echo @json_encode($inValue);
		exit;
	}
};