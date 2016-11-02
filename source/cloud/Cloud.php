<?php
# Namespace
namespace Cloud;

# Using
use Exception;
use Cloud\Libraries\Http\HttpRequest;
use Cloud\Libraries\Http\HttpResponse;
use Cloud\Libraries\SQL\SQL;

use Cloud\Exception\NotFoundException;
use Cloud\Exception\RpcNotImplementedException;
use Cloud\Exception\InvalidJsonFormatException;
use Cloud\Exception\InvalidJsonRpcFormatException;

/**
 * Cloud frame.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (C) 2016 Rodrigo Martins
 * @package   - CloudFrame
 * @version   - 2.0
 * @update	  - Cesar Ferreira
 */
class Cloud {
	/**
	 * Construct cloud.
	 * @param array $settings - Settings.
	 */
	public function __construct(array $settings=[]){
		# Request analysis
		$this->Analysis = new RequestAnalysis;
		
		# Request analysis
		$this->Dispatch = new DispatchHandler;
		
		# Loading database configuration
		require_once BASE_PATH.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'database.php';
		
		# SQL initialization
		if (!empty($db['host']))
		{
			new SQL($db['host'], $db['port'], $db['user'], $db['pass'], $db['bank']);
			foreach($db['queries'] as $query)
			{
				SQL::Query($query);
			}
		}
	}

	/**
	 * Binding trigger procedure.
	 * @param  string   $trigger - Trigger.
	 * @param   mixed $procedure - Procedure.
	 * @return   self
	 */
	public function Bind($trigger, $procedure){
		$this->Dispatch->Bind('BIND@'.$trigger, $procedure);
		return $this;
	}

	/**
	 * Register a route to a procedure.
	 * @param  string     $route - Route.
	 * @param   mixed $procedure - Procedure.
	 * @return   self
	 */
	public function Route($trigger, $procedure){
		$this->Dispatch->Bind('ROUTE@'.$trigger, $procedure);
		return $this;
	}


	/**
	 * Check that the Bind exists.
	 * @param  string $trigger - Trigger.
	 * @return   bool
	 */
	public function BindExists($trigger)
	{return $this->Dispatch->Exists('BIND@'.$trigger);}

	/**
	 * Check that the route exists.
	 * @param  string $route - Route.
	 * @return   bool
	 */
	public function RouteExists($route)
	{return $this->Dispatch->Exists('ROUTE@'.$route);}

	/**
	 * Run application.
	 * Sends the resultant response object to the HTTP client.
	 */
	public function Run(){
		try {
			// Request analysis
			$this->Analysis->Analyze(
				$this->Dispatch
			);
		}
		// Not found Exception
		catch (NotFoundException $e)
		{HttpResponse::Status(404);}
		// RpcNotImplementedException
		catch (RpcNotImplementedException $e)
		{HttpResponse::Status(501);}
		// InvalidJsonFormatException
		catch (InvalidJsonFormatException $e)
		{HttpResponse::Status(500);}
		// InvalidJsonRpcFormatException
		catch (InvalidJsonRpcFormatException $e)
		{HttpResponse::Status(500);}
		// Exception
		catch (Exception $e)
		{HttpResponse::Status(500);}
		
		// Send response
		if(!headers_sent()){
			// Status
			header(sprintf(
				'HTTP/%s %s %s',
				HttpRequest::ProtocolVersion(),
				HttpResponse::StatusCode(),
				HttpResponse::ReasonPhrase()
			));
			
			// Headers
			foreach(HttpResponse::Headers() AS $name => $value)
			header(sprintf('%s: %s', $name, $value));
		}
		
		// Content
		if(!HttpResponse::IsEmpty())
		echo HttpResponse::Content();
	}


	/** Request analysis */
	private $Analysis;

	/** Dispatch handler */
	private $Dispatch;
}