<?php
# Namespace
namespace Cloud\Libraries\Http;


# Using
use RuntimeException;
use InvalidArgumentException;


/**
 * Http Response.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (C) 2016 Rodrigo Martins
 * @package   - CloudFrame
 * @version   - 1.1
 */
class HttpResponse {
	/**
	 * With status code.
	 * @param    int         $code - Status code.
	 * @param string $reasonPhrase - Reason phrase.
	 */
	public static function Status($code, $reasonPhrase=''){
		if(!is_integer($code) || !isset(self::$HttpErrors[$code]))
		throw new InvalidArgumentException('Invalid HTTP status code');
		
		// Status
		self::$StatusCode = $code;
		
		// Reason
		if($reasonPhrase === '' && isset(self::$HttpErrors[$code]))
		self::$ReasonPhrase = self::$HttpErrors[$code];
		
		if(self::$ReasonPhrase === '')
		throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
	}

	/**
	 * Redirect.
	 * @param string    $url - Redirect destination.
	 * @param    int $status - Redirect HTTP status.
	 */
	public static function Redirect($url, $status=null){
		if(is_null($status)) $status = 302;
		
		self::Header('Location', $url);
		self::Status($code);
	}

	/**
	 * Http header.
	 * @param string  $name - Header name.
	 * @param string $value - Header value.
	 */
	public static function Header($name, $value)
	{self::$Headers[$name] = $value;}

	/**
	 * Json.
	 * @param mixed     $data - The data.
	 * @param   int   $status - Status code.
	 * @param   int $encoding - Json encoding.
	 */
	public static function Json($data, $status=null, $encoding=0){
		$json = json_encode($data, $encoding);
		
		if($json === false)
		throw new RuntimeException(json_last_error_msg(), json_last_error());
		
		self::Content($json);
		self::ContentType('application/json');
		self::ContentCharset('UTF-8');
		
		if(isset($status)) self::Status($status);
		
		self::$Content = $json;
	}

	/**
	 * Content.
	 * @param  string $data - The data.
	 * @return string
	 */
	public static function Content($data=''){
		if(!empty($data)) self::$Content = $data;
		
		return self::$Content;
	}

	/**
	 * With content type.
	 * @param string $type - Content type.
	 */
	public static function ContentType($type){
		// Current charset
		$parts = explode('=', self::$Headers['Content-Type']);
		if(!isset($parts[1])) $charset = mb_internal_encoding();
		else $charset = strtoupper($parts[1]);
		
		self::$Headers['Content-Type'] = strtolower($type).'; charset='.$charset;
	}

	/**
	 * With content charset.
	 * @param string $charset - Charset.
	 */
	public static function ContentCharset($charset){
		// Current content type
		$parts = explode(';', self::$Headers['Content-Type']);
		$content_type = strtolower($parts[0]);
		
		self::$Headers['Content-Type'] = $content_type.'; charset='.$charset;
	}

	/**
	 * Headers.
	 * @return array
	 */
	public static function Headers()
	{return self::$Headers;}

	/**
	 * Status code.
	 * @return int
	 */
	public static function StatusCode()
	{return self::$StatusCode;}

	/**
	 * Reason phrase.
	 * @return string
	 */
	public static function ReasonPhrase(){
		if(!empty(self::$ReasonPhrase))
		return self::$ReasonPhrase;
		
		if(empty(self::$ReasonPhrase))
		return self::$HttpErrors[self::$StatusCode];
	}


	/**
	 * Is this response OK?
	 * @return bool
	 */
	public static function IsOk()
	{return (self::$StatusCode == 200);} 

	/**
	 * Is this response empty?
	 * @return bool
	 */
	public static function IsEmpty()
	{return in_array(self::$StatusCode, [204, 205, 304]);}

	/**
	 * Is this response informational?
	 * @return bool
	 */
	public static function IsInformational()
	{return in_array(self::$StatusCode, [100, 101, 102]);}

	/**
	 * Is this response a redirection?
	 * @return bool
	 */
	public static function IsRedirect()
	{return in_array(self::$StatusCode, [301, 302, 303, 307]);}
	
	/**
	 * Is this response forbidden?
	 * @return bool
	 */
	public static function IsForbidden()
	{return (self::$StatusCode == 403);}

	/**
	 * Is this response not Found?
	 * @return bool
	 */
	public static function IsNotFound()
	{return (self::$StatusCode == 404);}


	/** Content */
	private static $Content = '';

	/** Status code */
	private static $StatusCode = 200;

	/** Reason phrase */
	private static $ReasonPhrase = '';

	/** Headers */
	private static $Headers = [
		'X-Powered-By' => 'CloudFrame V:1.1',
		'Content-Type' => 'application/json; charset=UTF-8'
	];

	/** Http errors */
	private static $HttpErrors = [
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Checkpoint',
		
		// Successful 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		420 => 'Method Failure',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		444 => 'No Response',
		451 => 'Unavailable For Legal Reasons',
		495 => 'SSL Certificate Error',
		496 => 'SSL Certificate Required',
		497 => 'HTTP Request Sent to HTTPS Port',
		499 => 'Client Closed Request',
		
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
		530 => 'Site is frozen'
	];
}