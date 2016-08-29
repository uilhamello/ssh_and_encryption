<?php

/**
 * Baseado na classe Bcrypt do TBelem
 * @link   https://gist.github.com/3438461
 */
class Bcrypt extends Crypt{

	/**
	 * [hash description]
	 * @param  [type] $string [description]
	 * @param  [type] $cost   [description]
	 * @return [type]         [description]
	 */
	public static function hash($string, $cost = null) {
		if (empty($cost)) {
			$cost = self::$_defaultCost;
		}

		self::$_hash_string = self::generateHashStringWithSalt();
		return crypt($string, self::$_hash_string);
	}
	
	/**
	 * Check a hashed string
	 * 
	 * @param  string $string The string
	 * @param  string $hash   The hash
	 * 
	 * @return boolean
	 */
	public static function check($string, $hash) {
		return (crypt($string, $hash) === $hash);
	}
}