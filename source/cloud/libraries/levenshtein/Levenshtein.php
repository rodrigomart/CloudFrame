<?php
# Namespace
namespace Cloud\Libraries\Levenshtein;


# Definições.
define("DEL_COST",   1);
define("INS_COST",   1);
define("SUB_COST",   1);
define("TRANS_COST", 1);


/**
 * Levenshtein.
 * @author    - Rodrigo Martins
 * @license   - MIT License
 * @copyright - (c) 2016 - Rodrigo Martins
 * @package   - External
 * @version   - 1.0
 */
class Levenshtein {
	/**
	 * Gets the distance calculation.
	 * @param  string $one - Text one.
	 * @param  string $two - Text two.
	 * @return  float      - Distance.
	 */
	public static function GetDistance($one, $two){
		self::$TextOne = $one;
		self::$TextTwo = $two;
		
		self::GetMatrix();
		
		return 1 - (self::$Similarity / self::Distance());
	}
	
	/**
	 * Matrix calculation.
	 */
	private static function GetMatrix(){
		$cost=-1; $del=0; $sub=0; $ins=0; $trans=0;
		
		self::$Matrix = [[]];
		
		$oneSize = mb_strlen(self::$TextOne, 'UTF-8');
		$twoSize = mb_strlen(self::$TextTwo, 'UTF-8');
		
		for($i = 0; $i <= $oneSize; $i++)
		self::$Matrix[$i][0] = ($i>0? (self::$Matrix[$i-1][0] + DEL_COST) : 0);
		
		for($i = 0; $i <= $twoSize; $i++)
		self::$Matrix[0][$i] = ($i>0? (self::$Matrix[0][$i-1] + INS_COST) : 0);
		
		for($i = 1; $i <= $oneSize; $i++):
			// curchar for the first string
			$cOne = mb_substr(self::$TextOne, $i-1, 1, 'UTF-8');
			
			for($j = 1; $j <= $twoSize; $j++):
				// curchar for the second string
				$cTwo = mb_substr(self::$TextTwo, $j-1, 1, 'UTF-8');
				
				// compute substitution cost
				if(strcmp($cOne, $cTwo) == 0):
					$cost = 0;
					$trans = 0;
				else:
					$cost = SUB_COST;
					$trans = TRANS_COST;
				endif;
				
				// deletion cost
				$del = self::$Matrix[$i-1][$j] + DEL_COST;
				
				// insertion cost
				$ins = self::$Matrix[$i][$j-1] + INS_COST;
				
				// substitution cost
				// 0 if same
				$sub = self::$Matrix[$i-1][$j-1] + $cost;
				
				// compute optimal
				self::$Matrix[$i][$j] = min($del, $ins, $sub);
				
				// transposition cost
				if(($i > 1) && ($j > 1)):
					// last two
					$ccOne = mb_substr(self::$TextOne, $i-2, 1, 'UTF-8');
					$ccTwo = mb_substr(self::$TextTwo, $j-2, 1, 'UTF-8');
					
					// transposition cost is computed as minimal of two
					if(strcmp($cOne, $ccTwo)==0 && strcmp($ccOne, $cTwo)==0)
					self::$Matrix[$i][$j] = min(self::$Matrix[$i][$j], self::$Matrix[$i-2][$j-2] + $trans);
				endif;
			endfor;
		endfor;
		
		self::$Similarity = self::$Matrix[$oneSize][$twoSize];
	}
	
	/**
	 * Distance calculation.
	 * @return int - Distance.
	 */
	private static function Distance(){
		$oneSize = mb_strlen(self::$TextOne, 'UTF-8');
		$twoSize = mb_strlen(self::$TextTwo, 'UTF-8');
		
		// amx cost, result value
		$maxCost = 0;
		
		// is substitution cheaper that delete+insert?
		$subCost = min(SUB_COST, DEL_COST + INS_COST);
		
		// get common size
		$minSize = min($oneSize, $twoSize);
		$maxSize = max($oneSize, $twoSize);
		$extraSize = $maxSize - $minSize;
		
		// on common size perform substitution / delete+insert, what is cheaper
		$maxCost = $subCost * $minSize;
		
		// on resulting do insert/delete
		if($oneSize > $twoSize) $maxCost += $extraSize * DEL_COST;
		else $maxCost += $extraSize * INS_COST;
		
		return $maxCost;
	}
	
	
	/** Text one */
	private static $TextOne;
	
	/** Text two */
	private static $TextTwo;
	
	/** Comparison matrix */
	private static $Matrix;
	
	/** The similarity */
	private static $Similarity;
}