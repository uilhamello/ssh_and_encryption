<?php

class File{

    private static $move_to;
    private static $file;
    private static $message;
    private static $new_file;

    /**
     * [outputHTML description]
     * @param  [type] $path  [description]
     * @param  array  $words [description]
     * @return [type]        [description]
     */
    public static function outputHTML($path, $words = [])
    {
        if(self::is_file($path)){
            $output = "";
            ob_start();
            include($path);
            $output .= ob_get_contents();
            ob_end_clean();
            return self::changeContentKeyWords($output, $words);
        }else{
            die("File not exist ".$path);
            return false;
        }
    }

    /**
     * [changeContentKeyWords description]
     * @param  [type] $content [description]
     * @param  array  $words   [description]
     * @return [type]          [description]
     */
    public static function changeContentKeyWords($content, $words=[])
    {
        if(empty($words)){
            return $content;
        }
        foreach ($words AS $key => $value){
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        return $content;
    }

    public static function is_file($path)
    {
        return file_exists($path);
    }



    public static function upload()
    {
        if(empty(self::getMoveTo())){
            die('directory to upload has not been informed');
        }
        $file_tmp_name = self::getFile('tmp_name');
        if(!isset($file_tmp_name)){
            die('the file has not been provided to be upload');
        }

        $file_name = uniqid (time ()) ."_".md5(self::getFile('name'));
        $directory = self::getMoveTo();
        $extension = pathinfo (self::getFile('name'), PATHINFO_EXTENSION );
        $destiny = $directory."/".$file_name.".".$extension ;        
        if(move_uploaded_file( self::getFile('tmp_name'), $destiny)){
            self::$new_file = $destiny;
            $result = self::$new_file;
        }else{
            self::$file_message = "Error: Image could not be recorded.";
            $result = false;
        }

        return $result;
    }

    public static function hash_file($tmp_name = null)
    {   
        if(empty($tmp_name)){
            if(empty(self::getFile())){
                die('Error: No file provided to hash');
            }else{
                $tmp_name = self::getFile('tmp_name');
            }
        }
 
        return Crypt::hash_file($tmp_name);
    }

    public static function move_to($moveto)
    {
        return self::$move_to = $moveto;
    }

    /**
     * [getMoveTo description]
     * @return [type] [description]
     */
    public static function getMoveTo()
    {
        return self::$move_to;        
    }

    /**
     * [setFile description]
     * @param [type] $file [description]
     */
    public static function setFile($file)
    {
        self::$file = $file;
    }

    public static function getFile($key = null)
    {
        if(!empty($key)){
            if(array_key_exists($key, self::$file)){
                return self::$file[$key];
            }
        }

        return self::$file;
    }

    public static function getNewFileName()
    {
        return self::$new_file;
    }

}