<?php

/**
 * Baseado na classe Bcrypt do Thiago Belem 
 * @link   https://gist.github.com/3438461
 */
class PasswordHash extends FactoryCrypt{

	/**
	 * [hash description]
	 * @return [type] [description]
	 */
	public static function hash($string) 
	{
		return password_hash($string, PASSWORD_BCRYPT);
	}

	/**
	 * [check description]
	 * @param  [type] $string [description]
	 * @param  [type] $hash   [description]
	 * @return [type]         [description]
	 */
	public static function check($string, $hash) 
	{
		return (password_verify($string, $hash));
	}
}