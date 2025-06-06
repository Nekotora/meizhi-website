<?php
	
	define("_LANG", "ja");
	define("_DATABASE", "japan");
	define("_NO_SQL", false);
	
	$_KEY = "THONLY";
	
	if (!defined("_INC")) {
		define("_SETMODE", false);//普通页面
	} else {
		define("_SETMODE", false);//API
	}
	
	$cfg["timesnumber"] = "9";
	$cfg["times"] = "第九回";
	$cfg["Year"] = date("Y");
	
	define("_SITEROOT", "http://shthonly.org/th09/ja");
	$cfg["root"] = _SITEROOT;
	$root = $cfg["root"];
	/* 路径设置 */
	$cfg["errofile"] = _BASE."/errorlist.ini";
	$cfg["jsonPath"] = _BASE."/json/";
	$cfg["csvPath"] = _BASE."/csv/";
	#HTML文件路径设定
	$cfg["tplPath"] = _BASE."/view/";
	#CSS文件路径设定
	$cfg["cssPath"] = $root."/style/";
	#基础图片路径设定
	$cfg["imagePath"] = $root."/style/image/";
	#JS文件路径设定
	$cfg["jsPath"] = $root."/js/";
	$cfg["jqueryPath"] = $cfg["jsPath"]."jquery-3.1.1.min.js";
	#模块文件路径设定
	$cfg["modulePath"] = _BASE."/module/";
	define("_MPATH", $cfg["modulePath"]);
	#函数文件路径设定
	$cfg["functionPath"] = _BASE."/function/";
	define("_FPATH", $cfg["functionPath"]);
	#类文件路径设定
	$cfg["classPath"] = _BASE."/class/";
	define("_CPATH", $cfg["classPath"]);
	#语言文件路径设定
	$cfg["langPath"] = _BASE."/lang/";
	define("_LPATH", $cfg["langPath"]);