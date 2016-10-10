<?php
# Namespace
namespace Cloud\Http;


/**
 * Http Request.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (C) 2016 Rodrigo Martins
 * @package   - CloudFrame
 * @version   - 1.1
 */
class HttpRequest {
	/**
	 * Protocol version.
	 * @return string
	 */
	public static function ProtocolVersion(){
		if(!isset($_SERVER['SERVER_PROTOCOL'])) return '1.0';
		
		$parts = explode('/', $_SERVER['SERVER_PROTOCOL']);
		return $parts[1];
	}

	/**
	 * Method.
	 * @return string
	 */
	public static function Method()
	{return $_SERVER['REQUEST_METHOD'];}

	/**
	 * Scheme.
	 * @return string
	 */
	public static function Scheme()
	{return $_SERVER['REQUEST_SHEME'];}

	/**
	 * Host.
	 * @return string
	 */
	public static function Host()
	{return $_SERVER['HTTP_HOST'];}

	/**
	 * Remote address.
	 * @return string
	 */
	public static function Address()
	{return $_SERVER['REMOTE_ADDR'];}

	/**
	 * Remote port.
	 * @return int
	 */
	public static function Port()
	{return intval($_SERVER['REMOTE_PORT']);}

	/**
	 * Fetch POST data.
	 * @param  string $key - Key name.
	 * @return  mixed
	 */
	public static function Post($key)
	{return self::MatrixSearch($key, $_POST);}

	/**
	 * Fetch GET data.
	 * @param  string $key - Key name.
	 * @return  mixed
	 */
	public static function Get($key)
	{return self::MatrixSearch($key, $_GET);}

	/**
	 * Uri.
	 * @return string
	 */
	public static function Uri(){
		$base = strtolower(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME));
		$base = str_ireplace ("\\", '', $base);
		
		$path = parse_url($_SERVER['REQUEST_URI'])['path'];
		$path = str_ireplace($base, '', $path);
		
		$paths = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
		return implode("/", $paths);
	}

	/**
	 * Content.
	 * @return string
	 */
	public static function Content(){
		return file_get_contents('php://input');
	}

	/**
	 * Content type.
	 * @return string
	 */
	public static function ContentType(){
		if(!isset($_SERVER['Content-Type'])) return 'text/plain';
		
		$parts = explode(';', $_SERVER['Content-Type']);
		return strtolower($parts[0]);
	}

	/**
	 * Content charset.
	 * @return string
	 */
	public static function ContentCharset(){
		if(!isset($_SERVER['Content-Type'])) return mb_internal_encoding();
		
		$parts = explode('=', $_SERVER['Content-Type']);
		if(!isset($parts[1])) return mb_internal_encoding();
		else return strtoupper($parts[1]);
	}


	/**
	 * Is this a GET request?
	 * @return bool
	 */
	public static function IsGet()
	{return (self::Method() === 'GET');}

	/**
	 * Is this a PUT request?
	 * @return bool
	 */
	public static function IsPut()
	{return (self::Method() === 'PUT');}

	/**
	 * Is this a PATCH request?
	 * @return bool
	 */
	public static function IsPatch()
	{return (self::Method() === 'PATCH');}

	/**
	 * Is this a DELETE request?
	 * @return bool
	 */
	public static function IsDelete()
	{return (self::Method() === 'DELETE');}

	/**
	 * Is this a POST request?
	 * @return bool
	 */
	public static function IsPost()
	{return (self::Method() === 'POST');}

	/**
	 * Is this a XREQUEST request?
	 * @return bool
	 */
	public static function IsXhr()
	{return isset($_HEADER['X_REQUESTED_WITH']);}


	/**
	 * Search Matrix.
	 *
	 * @param  string   $needle - Search.
	 * @param   array $haystack - Matrix.
	 * @return  mixed
	 */
	private static function MatrixSearch($needle, $haystack){
		$indexes = preg_split('/[:]/', strtolower($needle), 2, PREG_SPLIT_NO_EMPTY);
		
		if(is_numeric($indexes[0]))
		$indexes[0] = doubleval($indexes[0]);
		
		if(isset($haystack[$indexes[0]])):
			if(is_array($haystack[$indexes[0]]) && isset($indexes[1]))
			return self::MatrixSearch($indexes[1], $haystack[$indexes[0]]);
			
			return $haystack[$indexes[0]];
		endif;
		
		return null;
	}
}