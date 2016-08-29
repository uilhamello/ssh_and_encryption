<?php

class EncryptedText extends Model{

	protected $table = 'encryted_texts';
	protected $id = 'id';

	public function __construct()
	{
		parent::__construct();
	}
}