<?php
# Namespace
namespace Cloud;


# Using
use Exception;
use RuntimeException;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;


/**
 * Dispatch Handler.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (C) 2016 Rodrigo Martins
 * @package   - CloudFrame
 * @version   - 2.0
 * @update    - Cesar Ferreira
 */
class DispatchHandler {
	/**
	 * Binding trigger procedure.
	 * @param  string   $trigger - Trigger.
	 * @param   mixed $procedure - Procedure.
	 * @return   self
	 */
	public function Bind($trigger, $procedure){
		if(!is_string($trigger))
		throw new InvalidArgumentException('It was expected a string for the name of the procedure');
		
		$this->Triggers[] = $trigger;
		
		// Treatment for objects
		if(is_string($procedure) && strpos($procedure, '@') !== false)
		$procedure = preg_split('/@/i', $procedure, -1, PREG_SPLIT_NO_EMPTY);
		
		if(is_array($procedure) && is_string($procedure[0]))
		{
			$class = 'App\Controllers\\'.$procedure[0];
			$procedure[0] = new $class;
		}
		
		$this->Procedures[strtoupper($trigger)] = $procedure;
		return $this;
	}

	/**
	 * Procedure trigger exists?
	 * @param  string $trigger - Trigger.
	 * @return   bool
	 */
	public function Exists($trigger){
		if(isset($this->Procedures[strtoupper($trigger)])) return true;
		
		// Deep scan
		$subject = $trigger;
		foreach($this->Triggers AS $trigger){
			if(!preg_match(
				'/^'.str_ireplace(
					['/','(:any)','(:num)'],
					['\/','([\w\d]+)','([\d]+)'],
					trim($trigger, '/')
				).'$/i',
				trim($subject, '/'),
				$matches
			)) continue;
			
			return true;
		}
		
		return false;
	}

	/**
	 * Invoke procedure.
	 * @param  string $procedure - Procedure name.
	 * @param   array $arguments - Arguments.
	 * @return  mixed
	 */
	public function Invoke($trigger, $arguments=[]){
		// Deep scan and run
		$subject = $trigger;
		foreach($this->Procedures AS $trigger => $procedure){
			if(!preg_match(
				'/^'.str_ireplace(
					['/','(:any)','(:num)'],
					['\/','([\w\d]+)','([\d]+)'],
					trim($trigger, '/')
				).'$/i',
				trim($subject, '/'),
				$matches
			)) continue;
			
			// Matches for arguments
			array_shift($matches);
			if(empty($arguments)) $arguments = $matches;
			
			// Reflection
			if(!is_array($procedure)) $reflection = new ReflectionFunction($procedure);
			else $reflection = new ReflectionMethod($procedure[0], $procedure[1]);
			
			// Procedure arguments
			if(count($arguments) < $reflection->getNumberOfRequiredParameters())
			throw new InvalidArgumentException('Wrong number of arguments');
			
			if(count($arguments) > $reflection->getNumberOfParameters())
			throw new InvalidArgumentException('Too many arguments');
			
			// Named arguments
			if(array_keys($arguments) !== range(0, count($reflection->getParameters()) - 1)):
				$newArguments = [];
				
				foreach($reflection->getParameters() AS $param):
					$name = $param->getName();
					
					if(isset($arguments[$name]))
					{$newArguments[$name] = $arguments[$name];}
					
					elseif($param->isDefaultValueAvailable())
					{$newArguments[$name] = $param->getDefaultValue();}
					
					else
					{throw new InvalidArgumentException('Missing argument: '.$name);}
				endforeach;
				
				$arguments = $newArguments;
			endif;
			
			// Invoke
			if(!is_array($procedure)) return $reflection->invokeArgs($arguments);
			else return $reflection->invokeArgs($procedure[0], $arguments);
		}
		
		throw new RuntimeException('Unable to find the procedure');
	}


	/** Triggers */
	private $Triggers = [];

	/** Procedures */
	private $Procedures = [];
}