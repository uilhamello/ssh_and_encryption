<?php

class UserFile extends Model{

	protected $table = 'user_files';
	protected $id = 'id';

	protected $fillable = ['name', 'file','hash_original', 'current_hash', 'user_id','status', 'file_original_name','status_id', 'last_auditing', 'accessed', 'modified'];

	protected $timestamp = true;

	public function __construct()
	{
		parent::__construct();
	}
}