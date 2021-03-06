<?php

namespace Base;

use Exception;


class ApiModule extends Module
{
	public function run($inAction)
	{
		try
		{
			$parameters = $this->getActionParameters($inAction);
			
			// Check action
			if (! is_array($parameters))
			{
				throw new ApiException(ApiException::UNKNOWN_ACTION, 'Unknown action: ' . $inAction);
			}
			
			if (! $this->checkPermissions($inAction))
			{
				throw new ApiException(ApiException::PERMISSION_DENIED, 'Permission denied');
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
		header('Content-Type: application/json');
		echo @json_encode($inValue, JSON_PRETTY_PRINT);
		exit;
	}
};
