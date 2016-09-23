<?php
# Namespace
namespace Cloud;


# Using
use Exception;
use Cloud\Http\HttpRequest;
use Cloud\Exception\NotFoundException;
use Cloud\Exception\RpcNotImplementedException;
use Cloud\Exception\InvalidJsonFormatException;
use Cloud\Exception\InvalidJsonRpcFormatException;


/**
 * Request Analysis.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (C) 2016 Rodrigo Martins
 * @package   - CloudFrame
 * @version   - 1.1
 */
class RequestAnalysis {
	/**
	 * Analysis.
	 * Request analyzer and interpreting.
	 */
	public function Analyze($dispatch){
		if(
			HttpRequest::IsPost() &&
			HttpRequest::ContentType() == 'application/json'
		){
			// JSON data
			$json_data = json_decode(HttpRequest::Content(), true);
			
			// Invalid JSON format
			if(!is_array($json_data))
			throw new InvalidJsonFormatException('Malformed JSON');
			
			// Invalid JSON RPC format
			if(
				!isset($json_data['method'])     ||
				!is_string($json_data['method']) ||
				(
					isset($json_data['params'])  &&
					!is_array($json_data['params'])
				)
			) throw new InvalidJsonRpcFormatException('Malformed JSON RPC');
			
			// Trigger by JSON RPC
			$trigger = $json_data['method'];
			
			// Not implemented
			if(!$dispatch->Exists($trigger))
			throw new RpcNotImplementedException();
			
			// Params by JSON RPC
			if(!isset($json_data['params'])) $dispatch->Invoke($trigger);
			else $dispatch->Invoke($trigger, $json_data['params']);
		} else {
			// Trigger by URI
			$trigger = HttpRequest::Uri();
			
			// Not found
			if(!$dispatch->Exists($trigger))
			throw new NotFoundException();
			
			// Invoke
			$dispatch->Invoke($trigger);
		}
	}
}