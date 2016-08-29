<?php

class UserCommand extends Model{

	protected $table = 'user_commands';
	protected $id = 'id';

	protected $fillable = ['command'];

	protected $timestamp = true;

	public function __construct()
	{
		parent::__construct();
	}
}