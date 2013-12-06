<?php
function validate_between($check, $min, $max)
{
$n = mb_strlen($check);
return $min <= $n && $n <= $max;
}

function check_session(){
    if(!isset($_SESSION['username'])){
	    header("Location: /thread/index");
        die();
    }	
}

function session_ongoing(){
    if(isset($_SESSION['username'])){
	    header("Location: /thread/login_end");
			$username = $_SESSION['user'];
        return TRUE;
    }	
	return FALSE;
}