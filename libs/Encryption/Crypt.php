<?php

class Crypt extends PasswordHash{

	/**
	 * [enc description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	public static function enc($string)
	{
		//Unique IV
		self::$_iv = self::generateIV();

		self::$_hash_string = self::generateHashStringWithSalt();

		$result = openssl_encrypt($string, self::$encrypt_method, self::$_hash_string, 0,self::$_iv);
		return self::$_iv.$result;
	}

	/**
	 * [denc description]
	 * @param  [type]  $string [description]
	 * @param  [type]  $hash   [description]
	 * @param  boolean $local  [description]
	 * @return [type]          [description]
	 */
	public static function denc($string, $hash)
	{
		return openssl_decrypt(substr($string, self::getIVSize()), self::$encrypt_method, $hash, 0, self::getStringIV($string));
	}

	// public static function hash_file($tmp_name)
	// {	
	// 	if(isset($tmp_name)) {
	// 	    return sha1_file($tmp_name);
	// 	}
	// }

	/**
	 * [hash_file description]
	 * @param  [type] $tmp_name [description]
	 * @return [type]           [description]
	 */
	public static function hash_file($tmp_name)
	{	
		if(isset($tmp_name)) {
		    return hash_file('sha256',$tmp_name);
		}
	}

}