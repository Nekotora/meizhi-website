<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}

//全局函数

function vardump($var, $text = "") {
	if (defined("_SETMODE") && _SETMODE == true) {
		if ($text != "") {
			echo $text;
		}
		var_dump($var);
	} else {
		return false;
	}
}
function varecho($text, $line = 1) {
	if (defined("_SETMODE") && _SETMODE == true) {
		if ($line) {
			echo $text;
			echo str_repeat("\n<br />", $line);
		} else {
			echo $text;
		}
	} else {
		return false;
	}
}
function dumpPost() {
	var_dump($_POST);
	$temp = $_SESSION;
	unset($temp["langBase"]);
	unset($temp["langBase"]);
	var_dump($temp);
}