<?php

/**
 * 
 */
class FactoryCrypt{

	protected static $_hash_string;
	protected static $_saltPrefix = '2a';
	protected static $_defaultCost = 8;
	protected static $_saltLength = 22;
	protected static $_salt = 22;
	protected static $_concat = '##';
	protected static $encrypt_method = 'AES-256-CTR';
	protected static $_iv;

	/**
	 * Generate a random base64 encoded salt
	 * 
	 * @return string
	 */
	public static function generateRandomSalt() {
		// Salt seed
		$seed = uniqid(mt_rand(), true);
		// Generate salt
		$salt = base64_encode($seed);
		$salt = str_replace('+', '.', $salt);
		return substr($salt, 0, self::$_saltLength);
	}

	/**
	 * Build a hash string for crypt()
	 * 
	 * @param  integer $cost The hashing cost
	 * @param  string $salt  The salt
	 * 
	 * @return string
	 */
	protected static function generateHashString($salt,$cost=null) {
			if(!$cost){
				$cost = self::$_defaultCost;
			}
		return sprintf('$%s$%02d$%s$', self::$_saltPrefix, $cost, $salt);
	}

	/**
	 * [getHashString description]
	 * @return [type] [description]
	 */
	public static function getHashString()
	{
		return self::$_hash_string;
	}

	/**
	 * [generateHashStringWithSalt description]
	 * @return [type] [description]
	 */
	public static function generateHashStringWithSalt($cost = null)
	{
		//RandomSalt and hash
		self::$_salt = self::generateRandomSalt();
		//hash_string
		self::$_hash_string = self::generateHashString(self::$_salt, $cost);

		return self::$_hash_string;
	}

	/**
	 * [getIV description]
	 * @return [type] [description]
	 */
	public static function getIV()
	{
		return self::$_iv;
	}

	/**
	 * [generateIV description]
	 * @return [type] [description]
	 */
	public static function generateIV()
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		return mcrypt_create_iv($iv_size, MCRYPT_RAND);
	}

	/**
	 * [getStringIV description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	public static function getStringIV($string)
	{
		return substr($string, 0, self::getIVSize());
	}

	public static function getIVSize()
	{
		return mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	}

}