<?php

/**
 * [view description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function view($content)
{
	View::view($content);
}

function redirect_route($route)
{
	header("location:".$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']."/?module=".$route);
	exit;
}