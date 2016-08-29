<?php

class FactoryDatabase extends PDO{

	private $connect;
	private $rowNum;
	private $error;
	private $last_insert_id;
	private $stmt;
	private $queries;
	private $dbname;
	private $connection_name;

	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function __construct($_connect = NULL)
	{
		$this->setConnect($_connect);
	}

	/*
	 * Creating a conection by PDO
	*/
	private function connect($_CONFIG_DB, $_array_options =array())
	{

		if(empty($_array_options))
		{
	        $options = array(
	            PDO::ATTR_EMULATE_PREPARES    => false,
	            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
	        );
	    }

    	$connected = false;

	    if (is_array($_CONFIG_DB)) {
	        try{
	            $this->connect = new PDO("".$_CONFIG_DB["DRIVER"].":host=".$_CONFIG_DB["HOST"].";dbname=".$_CONFIG_DB["DBNAME"]."", $_CONFIG_DB["USER"], $_CONFIG_DB["PASSWORD"], $options);

        		$this->setDbName($_CONFIG_DB["DBNAME"]);
        		$connected = true;

        	}
	        catch(PDOException $e)
	        {
	            die($e->getMessage());
	        }
	    }

	    return $connected;
	}

	/*
	*
	*SetConnect('Connection_name_created_in_db_conection.ini')
	*Function: It set the conection will be used
	*Parameter: Connection name created in db_conection.ini
	*	
	*/
	public function setConnect($_connect = NULL)
	{
		//SEssion with Database configuration
		if(!isset($_session['LIB_XX_CONFIG_DB'])){
			require(SYS_CONFIG."database.php");
		}	

		if(!empty($_connect)){
			if(array_key_exists($_connect, $LIB_XX_CONFIG_DB)){
				$connected = $this->connect($LIB_XX_CONFIG_DB[$_connect]);
				if(!$connected){
					die(" Fatal Error: Database configuration not working");
				}

	            $this->setConnectionName($_connect);

			}
			else{
				die(" Fatal Error: Database configuration not existe");
			}

		}
		else{
			
			$CONFIG_VALID = false;

			/*check whether exist a default database configuration*/
			foreach ($LIB_XX_CONFIG_DB as $key => $value) {
				if(isset($value['default'])){
					if($value['default']){

						$CONNECTION_NAME = $key;
						$CONFIG_VALID = $value;
						break;
					}
				}
			}
			
			if(!$CONFIG_VALID){
					die("Fatal Error: There is not a valid database configuration.");
			}

			if ($this->connect($CONFIG_VALID)) {
	            $this->setConnectionName($CONNECTION_NAME);
			}

		}

	}

	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getConnect()
	{                  
		return $this->connect;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function setConnectionName($_connection_name)
	{

		$this->connection_name = $_connection_name;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getConnectionName()
	{

		return $this->connection_name;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function setDbName($_dbname)
	{

		$this->dbname = $_dbname;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getDbName()
	{
		return $this->dbname;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function setStmt($_stmt)
	{
		$this->stmt = $_stmt;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getStmt()
	{
		return $this->stmt;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getQueries()
	{
		return $this->queries;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function setQueries($_queries)
	{
		$this->queries = $_queries;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getRowNum()
	{
          
		return $this->rowNum;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function setRowNum($_rowNum)
	{

		$this->rowNum = $_rowNum;
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function resultset()
	{
	    try{
	    	if($this->stmtExecute())
	    	{
		    	return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	    	}
	    	else{
	    		return false;
	    	}
        }catch(PDOException $e)
        {
            die($e->getMessage());
        }
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function single()
	{
	    $this->stmtExecute();
	    return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function prepareQuery()
	{
		try{
	    	$this->stmt = $this->connect->prepare(self::getQueries());
		}
		catch(PDOException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function getLastInsertId()
	{
			return $this->connect->lastInsertId();
	}
	
	/**
	 * [__construct description]
	 * @param [type] $_connect [description]
	 */
	public function stmtExecute()
	{
		try{
			if(is_object($this->stmt))
			{
	
			    return $this->stmt->execute();
			}
			else{
				die("STMT is not a object");
			}
		}
		catch(PDOException $e)
		{
			die($e->getMessage());
		}
	}


	public function bind($array)
	{
		foreach ($array as $key => $value) {
			$type = null;
			if(isset($value['type'])){
				$type = $value['type'];
			}
			$this->bindexecute(':'.$key,$value['value'],$type);
		}
	}

	public function bindexecute($param, $value, $type = null){
	    if (is_null($type)) {
	        switch (true) {
	            case is_int($value):
	                $type = PDO::PARAM_INT;
	                break;
	            case is_bool($value):
	                $type = PDO::PARAM_BOOL;
	                break;
	            case is_null($value):
	                $type = PDO::PARAM_NULL;
	                break;
	            default:
	                $type = PDO::PARAM_STR;
	        }
	    }

    	if(is_object($this->stmt)){
   		    $this->stmt->bindValue($param, $value, $type);
    	}
    	else{
			die("STMT is not a object");
    	}

	}

}