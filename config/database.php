<?php

/**
 * Database Configuration:
 * That Project is using PDO extesion to stablish a database connection
 * @var [type]
 */
$LIB_XX_CONFIG_DB  = [
				"mysql_dev" => [
					"default" => true,
					"DRIVER" => "mysql",
					"HOST" => "localhost",
					"DBNAME" => "database_name",
					"USER" => "root",
					"PASSWORD" => ""
				],
				"mysql_prod" => [
					"DRIVER" => "mysql",
					"HOST" => "",
					"DBNAME" => "",
					"USER" => "",
					"PASSWORD" => ""
				],
				"pgsql_prod" => [
					"DRIVER" => "pgsql",
					"HOST" => "",
					"DBNAME" => "",
					"USER" => "",
					"PASSWORD" => ""
				],
			  ];



/**
 * Keeps the database configuration at a session avoiding to open this file more than once
 */
$_SESSION['LIB_XX_CONFIG_DB'] = $LIB_XX_CONFIG_DB;