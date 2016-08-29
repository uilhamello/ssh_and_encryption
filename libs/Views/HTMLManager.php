<?php

class HTMLManager{

	public static $data;
	public static $view;
	public static $content;
	public static $template;
	public static $no_template;
	
	/**
	 * 
	 */
	public function __construct()
	{}

	/**
	 * 
	 */
	public static function display($view = '', $echo = true)
	{
		if(!empty($view)){
			self::$view = $view;
		}

		self::defaultData();
		self::getMessages();
		//Template
		if(!empty(self::$template)){
			if(!self::$no_template){
				self::$content = File::outputHTML(self::$template, self::$data);
			}
		}

		//Content view
		$_content = '';
		if(!empty(self::$view)){
			//If it is a path of a file
			if(File::is_file(APP_VIEWS.self::$view)){
				$_content = File::outputHTML(APP_VIEWS.self::$view, self::$data);
			} else {				
				$_content = File::changeContentKeyWords(self::$view, self::$data);
			}
		}

		$_content = [ 'content' => $_content ];

		self::$content = File::changeContentKeyWords(self::$content, $_content);

		if($echo){
			echo self::$content;
		} else {
			return self::$content;
		}

	}

	public static function data($array)
	{
		if(!is_array($array)){
			return false;
		}

		self::$data = array_merge((array)self::$data, $array);
	}

	public static function defaultData()
	{	
		self::data($_SERVER);
		$url = ['URL_BASE' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']];
		self::data($url);

		if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){

			$user_session = [
				'session_name'=>$_SESSION['name'],
				'session_username'=>$_SESSION['username'],
				'session_id'=>$_SESSION['user_id']
			];
			self::data($user_session);
		}

		if(isset($_SESSION['view_to_message'])){
			View::data($_SESSION['view_to_message']);
			unset($_SESSION['view_to_message']);
		}

	}

	public static function view($view)
	{
		self::$view = $view;
	}

	public static function template($path)
	{
		self::$template = $path;
	}

	public static function getMessages()
	{
		if(array_key_exists('message', self::$data)){

			$msn = "<p class=\"alert {{alert-class}}\">{{message}}</p>";

			if(array_key_exists('alert-class', self::$data)){
				$class = self::$data['alert-class'];
			}else{				
				$class = 'alert-info';
			}

			$array = ['message'=>self::$data['message'], 'alert-class'=>$class];			
			self::$data['message'] = self::$content = File::changeContentKeyWords($msn, $array);;
		}else{
			self::$data['message'] ='';
		}
	}

	public static function old_record()
	{
		if(isset($_POST)){
			self::data($_POST);
		}
	}
}