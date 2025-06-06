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
		$this->tpl = "index01";
		return true;
	}
}