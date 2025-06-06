<?php
define("_BASE", ".");
$Base = _BASE;
require_once($Base."/config.php");			//基础配置
require_once($Base."/core/header.php");	//公共头
require_once($Base."/core/register.php");	//注册器
//require_once($Base."/core/database.php");	//数据库
require_once($Base."/core/module.php");	//数据库
require_once($Base."/core/function.php");	//函数

//require_once($Base."/core/class.geetestlib.php");	//函数
//require_once($Base."/CaptchaConfig.php");	//函数

require_once($Base."/core/display.php");	//语言

if (isset($_GET["v"]) && is_realstr($_GET["v"])) {
	$mode = $_GET["v"]."Module";
	if (class_exists($mode, true)) {
		$object = new $mode();
		$object->run();
	} else {
		$lang = $REG->error(3001, false);
		$lang["jsPath"] = $REG->CFG("jsPath");
		$lang["cssPath"] = $REG->CFG("cssPath");
		$lang["cssFilePath"] = $REG->CFG("cssPath") . "message.css";
		$lang["p"] .= $_GET["v"];
		include_safe($REG->CFG("tplPath")."message.php", $lang);
	}
} else {
	$object = new indexModule();
	$object->run();
}