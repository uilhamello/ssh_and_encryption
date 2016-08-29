<?php

class Model extends SQLBuilder{

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable($this->table);
		$this->setIdName($this->id);
		if(isset($this->timestamp)){
			$this->setTimeStamp($this->timestamp);
		}
	}

}