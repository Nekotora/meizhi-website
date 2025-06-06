<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
class indexModule extends module {
	public $name = "index";
	protected function checkGET() {
		return "read";
	}
	protected function checkPOST() {
		return "read";
	}
	protected function read() {
		if (isset($_GET["list"]) && $_GET["list"] == 1) {
			$this->tpl = "index02";
		} elseif (isset($_GET["list"]) && $_GET["list"] == 2) {
			$this->tpl = "index01";
		} else {
			$this->tpl = "index03";
		}
		return true;
	}
}