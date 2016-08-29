<?php
/**
 * [$ACCESS_WITHOUT_LOGGIN description]
 * @var [type]
 */
$ACCESS_WITHOUT_LOGGIN = [
	'checklogin' => [
		'controller' => 'LoginController',
		'method' => 'login',
		'view' => 'CheckLogin',
	],
	'register_view' => [
		'controller' => 'LoginController',
		'method' => 'register_view',
		'view' => 'register.html',
	],
	'register' => [
		'controller' => 'LoginController',
		'method' => 'register',
		'view' => '',
	],
];

/**
 * [$ACCESS_WITHOUT_LOGGIN description]
 * @var [type]
 */
$ROUTE_MODULES = [
	/*****User ******************************/
	'dashboard' => [
		'controller' => 'DashboardController',
		'method' => 'index',
		'view' => 'Olá, bem vindo!',
	],
	'user' => [
		'controller' => 'User',
		'method' => 'index',
		'view' => 'index.html',
	],
	'logout' => [
		'controller' => 'LoginController',
		'method' => 'logout',
		'view' => 'login.html'
	],
	/*****SSH Connection*******************************/
	'list_connection_ssh' => [
		'controller' => 'MachineController',
		'method' => 'index',
		'view' => 'machine/index.html'
	],
	'insert_conection' => [
		'controller' => 'MachineController',
		'method' => 'insert_conection',
		'view' => ''
	],
	'ssh_connection' => [
		'controller' => 'MachineController',
		'method' => 'shell',
		'view' => 'machine/shell.html'
	],
	'ssh_command' => [
		'controller' => 'MachineController',
		'method' => 'execute_command',
		'view' => '',
		'template' => false
	],
	'ssh_command_updown' => [
		'controller' => 'MachineController',
		'method' => 'command_updown',
		'view' => '',
		'template' => false
	],
	/*****Encrypt*******************************/
	'crypt_text' => [
		'controller' => 'EncryptedTextController',
		'method' => 'index',
		'view' => 'encrypted_text/index.html'
	],
	'crypt_text_action' => [
		'controller' => 'EncryptedTextController',
		'method' => 'crypt_text',
		'view' => 'encrypted_text/index.html'
	],

	/*****Auditing Files*******************************/
	'upload_file_list' => [
		'controller' => 'UserFileController',
		'method' => 'index',
		'view' => 'user_file/index.html'
	],
	'upload_file_action' => [
		'controller' => 'UserFileController',
		'method' => 'uploadFile',
		'view' => 'user_file/index.html'
	],
	'upload_file_auditing' => [
		'controller' => 'UserFileController',
		'method' => 'upload_file_auditing',
		'view' => ''
	],
];

/**
 * [$LOGIN description]
 * @var [type]
 */
$LOGIN =[
	  'controller' => 'LoginController',
	  'method' => 'index',
	  'view' => 'login.html',
	];

/**
 * Keeps the database configuration at a session avoiding to open this file more than once
 */
$_SESSION['LIB_XX_ROUTE_MODULES'] =  $ROUTE_MODULES;
$_SESSION['LIB_XX_ACCESS_LOGGIN'] =  $LOGIN;
$_SESSION['LIB_XX_ACCESS_WITHOUT_LOGGIN'] = $ACCESS_WITHOUT_LOGGIN;