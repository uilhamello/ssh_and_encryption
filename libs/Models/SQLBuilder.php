<?php

class SQLBuilder{

	private $queries_execs = array();
	private $query_exec;
	private $query_exec_active = false;
	private $update_active = false;
	private $orderby_active = false;
	private $table;
	private $id_name;
	private $id_value;
	private $where = array();
	private $fields;
	private $id_type;
	private $class;
	private $error;
	private $limit;
	private $factory_db;
	private $time_stamp;
	private $bind_array;

	public function setTimeStamp($timestamp)
	{
		$this->time_stamp = $timestamp;
	}

	/**
	 * [__construct description]
	 */
	public function __construct($connection=NULL)
	{
		$this->factory_db = new FactoryDatabase($connection);
	}

	/**
	 * [find description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function find($id)
	{		
		return $this->select()->where($this->getIdName(), $id)->get();
	}

	public function all()
	{
		return $this->select()->get();
	}

	/**
	 * [setTable description]
	 * @param [type] $_table [description]
	 */
	public function setTable($_table)
	{
		$this->table = $_table;
	}

	/**
	 * [getTable description]
	 * @return [type] [description]
	 */
	public function getTable()
	{
		return $this->table;
	}	

	/**
	 * [setWhere description]
	 * @param [type] $_where [description]
	 */
	public function where($field, $value_or_operator, $value_or_null=NULL, $type=NULL)
	{
		//if it's not empty means there are a value to a specific sql'operator to use like '=', is not', 'like'
		if(!empty($value_or_null)){
			$value = $value_or_null;
			$operator = $value_or_operator;
		} else {
			$value = $value_or_operator;
			$operator = '=';			
		}
		if($this->query_exec_active){
			$this->query_exec .= ' and '.$field.' '.$operator."'".$value."'";
		}
		return $this;
	}

	public function orderby($array)
	{
		$orderby = '';
		if(! $this->orderby_active ){
			$orderby = ' order by ';
		}
		$first = true;
		foreach ($array as $key => $value) {
			if(!$first){
				$orderby .= ', ';
			}
			//if is numeric assume that it is just a field name without 'desc/asc' so uses a 'desc' ordenation as default
			if(is_numeric($key)){ 
				$orderby .= $value;
			} else {				
				$orderby .= $key.' '.$value;
			}

			$first = false;
		}

		if($this->query_exec_active){
			$this->query_exec .= $orderby;
		}

		return $this;
	}

	/**
	 * [getWhere description]
	 * @return [type] [description]
	 */
	public function getWhere()
	{
		$where = '';
		if((!empty($this->where)) and (is_array($this->where))){
			$first = true;
			foreach ($this->where as $key => $value) {
				if(!$first){
					$where .= ' and ';
				}
				$where .= $value['field'].' '.$value['operator']."'".$value['value']."'";
				$first = false;
			}
		}
		return $where;
	}

	/**
	 * [setIdValue description]
	 * @param [type] $_id_value [description]
	 */
	public function setIdValue($_id_value)
	{
		$this->id_value = $_id_value;
	}

	/**
	 * [getIdValue description]
	 * @return [type] [description]
	 */
	public function getIdValue()
	{
		return $this->id_value;
	}

	/**
	 * [setIdName description]
	 * @param [type] $_id_name [description]
	 */
	public function setIdName($_id_name)
	{
		$this->id_name = $_id_name;
	}

	/**
	 * [getIdName description]
	 * @return [type] [description]
	 */
	public function getIdName()
	{
		return $this->id_name;
	}

	/**
	 * [setLimit description]
	 * @param [type] $_limit [description]
	 */
	public function setLimit($_limit)
	{

		$this->limit = $_limit;
	}

	/**
	 * [getLimit description]
	 * @return [type] [description]
	 */
	public function getLimit()
	{

		return $this->limit;
	}

	public function setFields($_fields){

		if(!empty($_fields)){

			$this->fields = $_fields;

		}
	}

	public function getFields(){

		$_fields = $this->fields;

		if(is_array($this->fields)){
			
			$_fields = '';

			for($i=0; $i < count($this->fields); $i++){ 
				
				if($i > 0){

					$_fields .=', ';
				}

				$_fields .=$this->fields[$i];
			}

		}		
		return $_fields;
	}

	/**
	 * [select description]
	 * @return [type] [description]
	 */
	public function select($table = NULL)
	{
		if(!empty($table)){
			$this->setTable($table);
		}
		if(trim($this->getTable()) !=""){
			
			$_where = ' 1 = 1 ';
			$_fields ='*';

			if( trim($this->getFields()) != "")
			{
				$_fields = $this->getFields();
			}

			$this->query_exec = "select ".$_fields." from ".$this->getTable()." where ".$_where;
			$this->query_exec_active = true;

			return $this;

		}else{
			die("Data Base Error:  The table name has not been provided.");
		}		
	}

	public function insert($array_values)
	{
		if(!isset($this->fillable) or empty($this->fillable)){
			die('Error: Please, you need to inform the \'Fillable\' table fields in your models');
		}
		$table_fields = '';
		$fields_values = '';
		$first = true;
		$bind_array = [];
		foreach ($this->fillable as $key => $value) {
			if(array_key_exists($value, $array_values)){
				if(!$first){
					$table_fields  .= ',';
					$fields_values .= ',';
				}
				$table_fields  .= $value;
				$fields_values .= ":".$value."";
				$bind_array[$value] = ['value'=>$array_values[$value]];
				$first = false;
			}
		}

		if( $this->time_stamp && !$first ){
			$table_fields  .= ', created_at';
			$fields_values .= ", :created_at";	
			$bind_array['created_at'] = ['value'=> date('Y-m-d H:i:s')];
		}

		$this->bind_array = $bind_array;
		$this->query_exec = "insert into ".$this->table." (".$table_fields.") values (".$fields_values.")";

		if($this->exec()){
			return $this->factory_db->getLastInsertId();
		}
		else{
			return false;
		}
	}

	public function update($array_values)
	{

		if(!isset($this->fillable) or empty($this->fillable)){
			die('Error: Please, you need to inform the \'Fillable\' table fields in your models');
		}

		$table_fields = '';
		$fields_values = '';
		$first = true;
		$bind_array = [];
		$set_values = '';
		foreach ($this->fillable as $key => $value) {
			if(array_key_exists($value, $array_values)){
				if(!$first){
					$set_values  .= ',';
				}
				$set_values  .= $value." = ".":".$value."";
				$bind_array[$value] = ['value'=>$array_values[$value]];
				$first = false;
			}
		}

		if( $this->time_stamp && !$first ){
			$set_values  .= ', updated_at = :updated_at';
			$bind_array['updated_at'] = ['value'=> date('Y-m-d H:i:s')];
		}

		$_where = ' 1 = 1 ';

		$this->bind_array = $bind_array;
		$this->query_exec = "update ".$this->table." set ".$set_values." where ".$_where;
		$this->update_active = true;
		$this->query_exec_active = true;

		return $this;

	}


	public function get()
	{
		if($this->update_active){
			return $this->exec();
		}else{

			$this->factory_db->setQueries($this->query_exec);
			$this->factory_db->prepareQuery();
			
			if( $result = $this->factory_db->resultset()){
				$this->setQueriesExecs($this->query_exec);
			}

			return $result;

		}

	}

	/**
	 * [exec description]
	 * @return [type] [description]
	 */
	public function exec()
	{
		$this->factory_db->setQueries($this->query_exec);
		$this->factory_db->prepareQuery();
		if(!empty($this->bind_array)){
			$this->factory_db->bind($this->bind_array);
		}
	    return $this->factory_db->stmtExecute();
	}

	public function setQueriesExecs($query)
	{
		$this->queries_execs[] = $query;

	}

	public function getQueriesExecs()
	{
		return $this->queries_execs;
	}

}