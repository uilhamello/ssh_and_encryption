<?php

class Route{

	protected static $without_session = [];
	protected static $module_routes = [];
	
	private static function setRoutes()
	{
		// if(!isset($_SESSION['LIB_XX_ACCESS_WITHOUT_LOGGIN'])
		// 	or !isset($_SESSION['LIB_XX_ROUTE_MODULES'])
		// 	or !isset($_SESSION['LIB_XX_ACCESS_LOGGIN'])){
		// }
		require(SYS_CONFIG."routes.php");
	}

	public static function getLoginRoute()
	{
		self::setRoutes();
		return $_SESSION['LIB_XX_ACCESS_LOGGIN'];
	}

	public static function setWithoutSession()
	{
		if(empty(self::$without_session)){
			self::setRoutes();
			self::$without_session = $_SESSION['LIB_XX_ACCESS_WITHOUT_LOGGIN'];
		}
	}

	public static function getWithoutSessionRoute($module = '')
	{
		self::setWithoutSession();

		//return a especifc module
		if(!empty($module)){
			if(isset(self::$without_session[$module])){
				return self::$without_session[$module];
			}else{
				die("Error: Route '".$module."' is not configured");
			}
		}
		else{
			return self::$without_session;
		}
	}

	public static function getModuleRoute($module = '')
	{
		self::setModuleRoute();

		//return a especifc module
		if(!empty($module)){
			if(isset(self::$module_routes[$module])){
				return self::$module_routes[$module];
			}else{
				die("Error: Route '".$module."' is not configured");
			}
		}
		else{
			return self::$module_routes;
		}
	}

	public static function setModuleRoute()
	{
		if(empty(self::$module_routes)){
			self::setRoutes();
			self::$module_routes = $_SESSION['LIB_XX_ROUTE_MODULES'];
		}
	}

}