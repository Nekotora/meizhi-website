<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
abstract class module {
	public $name = "module";
	protected $NeedUser = false;
	protected $user = array();
	protected $id = 0;
	protected $REG;
	protected $Ary = array();
	protected $action = "";
	protected $lang;
	protected $DB = array();
	protected $method = "";
	protected $tpl = "";
	protected $param = array();
	protected $error = "0000";
	protected $cookieWord = _KEY . "_LOGIN";
	function __construct() {
		$this->REG = &register::load();
	}
	function __toString() {
		
	}
	function __invoke() {
		$this->run();
	}
	function __call($functionName, $args){
		$this->error(3003, $functionName);
	}
	protected function show() {
		$this->lang["jsPath"] = $this->REG->CFG("jsPath");
		$this->lang["cssPath"] = $this->REG->CFG("cssPath");
		$this->lang["cssFilePath"] = $this->REG->CFG("cssPath") . "message.css";
		if (!isset($this->lang["h2"])) {
			$this->lang["h2"] = "Undefined Title";
		}
		if (!isset($this->lang["h3"])) {
			$this->lang["h3"] = "Undefined SubTitle";
		}
		if (!isset($this->lang["p"])) {
			$this->lang["p"] = "Undefined Text";
		}
		if (!isset($this->lang["url"])) {
			$this->lang["url"] = "/";
		}
		if (!isset($this->lang["link"])) {
			$this->lang["link"] = "Back to homepage";
		}
		include_safe($this->REG->CFG("tplPath")."message.php", $this->lang);
		exit();
	}
	protected function display() {
		if ($this->tpl == "") {
			$this->show();
		} else {
			$this->lang["jsPath"] = $this->REG->CFG("jsPath");
			$this->lang["jqueryPath"] = $this->REG->CFG("jqueryPath");
			$this->lang["cssPath"] = $this->REG->CFG("cssPath");
			$this->lang["jsFilePath"] = $this->REG->CFG("jsPath") . $this->name . "/" . $this->tpl . ".js";
			$this->lang["cssFilePath"] = $this->REG->CFG("cssPath") . $this->name . "/" . $this->tpl . ".css";
			$this->tpl = $this->name . "/" . $this->tpl . ".php";
		}
		include_safe($this->REG->CFG("tplPath"). $this->tpl, $this->lang);
		exit();
	}
	protected function display_ob() {
		if ($this->tpl == "") {
			$this->show();
		} else {
			$this->lang["jsPath"] = $this->REG->CFG("jsPath");
			$this->lang["jqueryPath"] = $this->REG->CFG("jqueryPath");
			$this->lang["cssPath"] = $this->REG->CFG("cssPath");
			$this->lang["jsFilePath"] = $this->REG->CFG("jsPath") . $this->name . "/" . $this->tpl . ".js";
			$this->lang["cssFilePath"] = $this->REG->CFG("cssPath") . $this->name . "/" . $this->tpl . ".css";
			$this->tpl = $this->name . "/" . $this->tpl . ".php";
		}
		return include_ob($this->REG->CFG("tplPath").$this->tpl, $this->lang);
	}
	protected function make() {
		if (is_realstr($this->action)) {
			$action = $this->action;
			$result = $this->$action();
			if ($result === true) {
				$this->display();
			} elseif (is_realstr($result)) {
				$this->action = $result;
				$this->make();
			} else {
				$this->error($this->error);
			}
		} else {
			$this->error(3003, $this->action);
		}
	}
	protected function error($error, $p = "") {
		if (isset($this->lang["url"])) {
			$back = $this->lang["url"];
		} else {
			$back = "";
		}
		$this->lang = $this->REG->error($error, false);
		if ($back != "") {
			$this->lang["url"] = $back;
		}
		if ($p != "") {
			$this->lang["p"] .= $p;
		}
		$this->show();
		exit();
	}
	abstract protected function checkGET();
	abstract protected function checkPOST();
	abstract protected function read();
	protected function ban() {
		$this->error(2001, $this->action);
		exit();
	}
	protected function updateUser() {
		$Du = new user();
		$this->user = $Du->selectID($_SESSION["user"]["id"]);
		if ($this->user["password"] != $_SESSION["user"]["password"]) {
			header('HTTP/1.1 302 Moved Permanently');
			header("Location: ".$this->REG->CFG("root")."/?v=logout");
		}
		$this->id = $this->user["id"];
		$_SESSION["user"] = $this->user;
		$this->cookie("write");
		if ($this->user["ip"] == "") {
			$Du->uptIP();
		}
		unset($Du);
	}
	protected function cookie($mode = "read") {
		$word = $this->cookieWord;
		if ($mode == "read") {
			if (isset($_COOKIE[$word])) {
				$temp = explode("||", $_COOKIE[$word]);
				if (is_numint($temp[1])) {
					if (isset($_SESSION["user"]) && $temp[1] == $_SESSION["user"]["id"]) {
						return true;
					}
					$Du = new user();
					$user = $Du->selectID($temp[1]);
					if ($user != false && isset($user["pwrt"]) && $user["pwrt"] == $temp[0] && md5($user["password"]) == $temp[2]) {
						$_SESSION["user"] = $user;
					} else {
						setcookie($this->cookieWord, "", time()-3600*24);
					}
				} else {
					return false;
				}
			}
		} elseif ($mode == "write") {
			if (!isset($this->user["pwrt"])) {
				return false;
			}
			setcookie($this->cookieWord, ($this->user["pwrt"] . "||" . $this->id . "||" . md5($this->user["password"])), time()+3600*24*30);
		}
		return true;
	}
	protected function getUser($id) {
		$Du = new user();
		$_SESSION["user"] = $this->user = $Du->selectID($id);
		unset($Du);
	}
	public function run() {
		$this->error = 3002;
		if ($this->NeedUser === true) {
			$this->cookie("read");
		}
		$result = $this->checkGET();
		if ($result !== true) {
			$this->action = $result;
		} else {
			$this->action = "read";
		}
		if ($this->action === false) {
			$this->error($this->error);
		}
		if ($this->NeedUser === true) {
			if (!isset($_SESSION["user"]["password"])) {
				$this->ban();
			}
			$this->updateUser();
		}
		$this->error = 3002;
		$result = $this->checkPOST();
		if ($result !== true) {
			$this->action = $result;
		}
		if ($this->action === false) {
			$this->error($this->error);
		}
		$this->make();
	}
}