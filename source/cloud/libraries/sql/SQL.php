<?php
# Namespace
namespace Cloud\Libraries\SQL;


# Using
use MySQLi;


/**
 * SQL.
 * @author    - Rodrigo Martins.
 * @license   - MIT License
 * @copyright - (C) 2016 - Rodrigo Martins
 * @package   - Database
 * @varsion   - 1.0
 */
class SQL {
	/**
	 * Create MySQL connection.
	 * @param  string $host - Host.
	 * @param     int $port - Port.
	 * @param  string $user - User.
	 * @param  string $pass - Pass.
	 * @param  string $bank - bank.
	 */
	public function __construct($host='localhost', $port=3306, $user='root', $pass='', $bank=''){
		try {
			// Create mysql connection.
			self::$MySQLiConnection = new MySQLi(
				$host,
				$user,
				$pass,
				$bank,
				$port
			);
			
			// Codification
			self::$MySQLiConnection->set_charset('utf8');
		}
		catch(Exception $e){}
	}
	
	
	/**
	 * Query.
	 *
	 * @param  string $query - Query command.
	 * @param   array $args  - Arguments.
	 * @return object
	 */
	public static function Query($query, $args=[]){
		// Replace args
		foreach($args AS $pattern => $replace)
		$query = str_ireplace($pattern, $replace, $query);
		
		// Cache hash
		$hash = hash('crc32', $query);
		
		// Cashed
		if(isset(self::$QueryCache[$hash]))
		return self::$QueryCache[$hash];
		
		// Query
		$data = self::$MySQLiConnection->query($query);
		
		// Cache querys
		if(preg_match('/SELECT/i', $query)){
			self::$QueryCache[$hash] =& $data;
			return self::$QueryCache[$hash];
		}
	}
	
	/**
	 * Num rows.
	 *
	 * @param  object $result - Query result.
	 * @return    int
	 */
	public static function NumRows($result)
	{return $result->num_rows;}
	
	/**
	 * Fetch object.
	 *
	 * @param  object $result - Query result.
	 * @return object
	 */
	public static function FetchObject($result)
	{return $result->fetch_object();}
	
	
	/** Cache querys */
	private static $QueryCache = [];
	
	/** MySQLi Connection */
	private static $MySQLiConnection;
}