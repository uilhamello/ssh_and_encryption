<?php

class User extends Model{

	protected $table = 'users';
	protected $id = 'id';

	protected $fillable = ['name', 'username','password'];

	protected $timestamp = true;

	public function __construct()
	{
		parent::__construct();
	}
}