<?php

class Machine extends Model{

	protected $table = 'machines';
	protected $id = 'id';

	protected $fillable = ['ip', 'user','password', 'user_id'];

	protected $timestamp = true;

	public function __construct()
	{
		parent::__construct();
	}
}