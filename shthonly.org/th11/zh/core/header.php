<?php
define( "_KEY", $_KEY );
	
	if (defined('_SETMODE')) {
		if (!_SETMODE && defined("_INC")) {
			error_reporting(0);
		} else {
			error_reporting(-1);
		}
	}
	require_once($Base."/core/debug.php");	//公共头
	if (!defined('_IS_API')) {
		header("Content-Type: text/html;charset=utf-8");
	}
	session_start();