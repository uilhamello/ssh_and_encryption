<?php

class Ssh2{

    private static $ip;
    private static $port = 22;
    private static $username;
    private static $password;
    private static $connection;
    public static $command;
    public static $data;
    public static $error;

    public static function check_suport()
    {
        return (function_exists("ssh2_connect"));
    }

    public static function connect($ip, $username, $password, $port = '22')
    {
        self::$ip = $ip;
        self::$username = $username;
        self::$password = $password;
        self::$port =  $port;

        if(self::checkIP()){
            if(self::authenticate()){
                return true;
            }
            self::$error = 'Bad Username or Password';
            return false;
        }else{
            self::$error = 'Bad IP';
            return false;            
        }
    }

    public static function command($command)
    {
         self::$command = $command;
         self::execute();
         return self::$data;
    }

    public static function execute()
    {
        if(!($stream = ssh2_exec(self::$connection, self::$command ))) {
            return false;
        }
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream,4096)) {
            $data .= $buf;
        }        
        fclose($stream);
        self::$data = $data;
    }

    private static function checkIP()
    {
        if(self::$connection = ssh2_connect(self::$ip, self::$port)){
            return true;
        } else{
            return false;
        }
    }

    private static function authenticate()
    {
        if(ssh2_auth_password(self::$connection, self::$username, self::$password)) {
            return true;
        } else {
            return false;
        }
    }
}
