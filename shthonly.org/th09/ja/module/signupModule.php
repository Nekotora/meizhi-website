<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
class signupModule extends module {
	public $name = "signup";
	protected function checkGET() {
		if (isset($_GET["a"]) && $_GET["a"] == "signup") {
			return "signup";
		} elseif (isset($_GET["a"]) && $_GET["a"] == "post") {
			return "post";
		}
		return "read";
	}
	protected function checkPOST() {
		if ($this->action == "signup") {
			return "signup";
		} elseif ($this->action == "post") {
			return "post";
		}
		return "read";
	}
	protected function read() {
		$this->tpl = "index01";
		return true;
	}
	
	protected function signup() {
		$this->tpl = "index02";
		return true;
	}
	protected function post() {
		
		$Ary = $work = [];
		
		if (empty($_POST["g-recaptcha-response"])) {
			die("<script>alert('reCaptchaをチェックしてください');window.history.back();</script>");
		}
		$recaptcha = $_POST["g-recaptcha-response"];
		
		$recaptcha_result = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcsV04UAAAAACYW5lJz_qMxKC2en2jaR_V-PNrX&response=" . $recaptcha . "&remoteip=" . GetIP());
		
		$Ary["recaptcha"] = $recaptcha_result;
		
		$recaptcha_result = json_decode($recaptcha_result, true);
		
		if ($recaptcha_result["success"] != true) {
			die("<script>alert('reCaptchaをチェックしてください');window.history.back();</script>");
		}
		
		if (empty($_POST["circle"])) {
			die("<script>alert('サークル名の情報の記入に誤りがある');window.history.back();</script>");
		} else {
			$Ary["name"] = $_POST["circle"];
		}
		if (empty($_POST["email"])) {
			die("<script>alert('メールアドレスの情報の記入に誤りがある');window.history.back();</script>");
		} else {
			$Ary["email"] = $_POST["email"];
		}
		if (empty($_POST["author"])) {
			die("<script>alert('ペンネームの情報の記入に誤りがある');window.history.back();</script>");
		} else {
			$Ary["author"] = $_POST["author"];
		}
		if (empty($_POST["website"])) {
			die("<script>alert('webサイトの情報の記入に誤りがある');window.history.back();</script>");
		} else {
			$Ary["website"] = $_POST["website"];
		}
		
		if (isset($_POST["space"])) {
			$Ary["space"] = $_POST["space"];
		}
		if (isset($_POST["nearby"])) {
			$Ary["nearby"] = $_POST["nearby"];
		}
		if (isset($_POST["textarea"])) {
			$Ary["textarea"] = $_POST["textarea"];
			$Ary["textarea"] = str_replace(array("\r\n", "\r", "\n"), '<br />', $Ary["textarea"]);
		}
		
		$Dw = new work();
		$i = 1;
		if (is_array($_POST["work".$i]) && !empty($_POST["work".$i]["name"])) {
			$work[$i]["name"] = $_POST["work".$i]["name"];
			
			if (empty($_POST["work".$i]["type"])) {
				die("<script>alert('必要な作品の情報の記入に誤りがある');window.history.back();</script>");
			} else {
				$work[$i]["type"] = $_POST["work".$i]["type"];
			}			
			if (empty($_POST["work".$i]["price"])) {
				die("<script>alert('必要な作品の情報の記入に誤りがある');window.history.back();</script>");
			} else {
				$work[$i]["price"] = $_POST["work".$i]["price"];
			}
		} else {
			die("<script>alert('必要な作品の情報の記入に誤りがある');window.history.back();</script>");
		}
		$Ary["work1"] = $Dw->addWork($work[$i]);
		$i = 2;
		if (is_array($_POST["work".$i]) && !empty($_POST["work".$i]["name"])) {
			$work[$i]["name"] = $_POST["work".$i]["name"];
			
			if (!empty($_POST["work".$i]["type"])) {
				$work[$i]["type"] = $_POST["work".$i]["type"];
			}
			if (!empty($_POST["work".$i]["price"])) {
				$work[$i]["price"] = $_POST["work".$i]["price"];
			}
			$Ary["work2"] = $Dw->addWork($work[$i]);
		}
		$i = 3;
		if (is_array($_POST["work".$i]) && !empty($_POST["work".$i]["name"])) {
			$work[$i]["name"] = $_POST["work".$i]["name"];
			
			if (!empty($_POST["work".$i]["type"])) {
				$work[$i]["type"] = $_POST["work".$i]["type"];
			}
			if (!empty($_POST["work".$i]["price"])) {
				$work[$i]["price"] = $_POST["work".$i]["price"];
			}
			$Ary["work3"] = $Dw->addWork($work[$i]);
		}
		
		$circle = _DATABASE;
		$Dc = new $circle();
		
		$result = $Dc->addCircle($Ary);
		
		if (!$result) {
			die("<script>alert('提出できませんでした。 もう一度お試しください');window.history.back();</script>");
		}
		
		$this->tpl = "index03";
		return true;
	}
}