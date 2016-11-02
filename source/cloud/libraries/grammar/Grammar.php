<?php
# Namespace
namespace Cloud\Libraries\Grammar;


/**
 * Grammar.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (c) 2016 - Rodrigo Martins
 * @package   - External
 * @version   - 1.0
 */
class Grammar {
	/**
	 * Generate bi-gram.
	 * @param  string $str  - Text.
	 * @param    char $glue - Glue to string format.
	 * @return  mixed
	 */
	public static function BiGram($str, $glue=null)
	{return self::nGram($str, 2, $glue);}
	
	/**
	 * Generate tri-gram.
	 * @param  string $str  - Text.
	 * @param    char $glue - Glue to string format.
	 * @return  mixed
	 */
	public static function TriGram($str, $glue=null)
	{return self::nGram($str, 3, $glue);}
	
	/**
	 * Generate quad-gram.
	 * @param  string $str  - Text.
	 * @param    char $glue - Glue to string format.
	 * @return  mixed
	 */
	public function QuadGram($str, $glue=null)
	{return self::nGram($str, 4, $glue);}
	
	/**
	 * Generate ngram.
	 * @param  string $str  - Text.
	 * @param     int $size - Grammar size.
	 * @param    char $glue - Glue to string format.
	 * @return  mixed
	 */
	private static function nGram($str, $size=2, $glue=null){
		$grams = [];
		
		$words = preg_split('/[\s]+/si', strtolower($str), -1, PREG_SPLIT_NO_EMPTY);
		
		foreach($words as $word):
			foreach(str_split($word, $size) as $ng):
				if(strlen(utf8_encode($ng)) == $size)
				$grams[] = utf8_encode($ng);
			endforeach;
		endforeach;
		
		if(is_null($glue)) return $grams;
		else return implode($glue, $grams);
	}
}