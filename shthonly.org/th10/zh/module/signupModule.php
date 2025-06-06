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
		$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
		
		$data = array(
				"user_id" => "926437eda2a7e515ee2d81d3dd29df42",
				"client_type" => "web",
				"ip_address" => GetIP()
		);
		
		$status = $GtSdk->pre_process($data, 1);
		$_SESSION['gtserver'] = $status;
		$_SESSION['user_id'] = $data['user_id'];
		$this->lang["captcha"] = $GtSdk->get_response_str();
		
		$this->tpl = "index02";
		return true;
	}
	protected function post() {
		if (empty($_POST["geetest_challenge"]) || empty($_POST["geetest_validate"])) {
			die("<script>alert('请完成表格最后的验证码再提交');window.history.back();</script>");
		}
		$geetest_challenge = $_POST["geetest_challenge"];
		$geetest_validate = $_POST["geetest_validate"];
		$geetest_seccode = $_POST["geetest_seccode"];
		
		$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
		
		$data = array(
				"user_id" => "926437eda2a7e515ee2d81d3dd29df42",
				"client_type" => "web",
				"ip_address" => GetIP()
		);
		
		if ($_SESSION['gtserver'] == 1) {   //服务器正常
			$capcha_result = $GtSdk->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode, $data);
			if ($capcha_result) {
				$captcha_result = true;
			} else{
				$captcha_result = false;
			}
		} else {  //服务器宕机,走failback模式
			if ($GtSdk->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode)) {
				$captcha_result = true;
			}else{
				$captcha_result = false;
			}
		}
		
		$Ary = $work = [];
		
		if ($captcha_result != true) {
			die("<script>alert('请完成表格最后的验证码再提交');window.history.back();</script>");
		}
		
		if (empty($_POST["circle"])) {
			die("<script>alert('请填写社团名');window.history.back();</script>");
		} else {
			$Ary["name"] = $_POST["circle"];
		}
		if (empty($_POST["email"])) {
			die("<script>alert('请填写电子邮箱');window.history.back();</script>");
		} else {
			$Ary["email"] = $_POST["email"];
		}
		if (empty($_POST["author"])) {
			die("<script>alert('请填写作者名或代表者名');window.history.back();</script>");
		} else {
			$Ary["author"] = $_POST["author"];
		}
		if (empty($_POST["website"])) {
			die("<script>alert('请填写社团官网或个人主页');window.history.back();</script>");
		} else {
			$Ary["website"] = $_POST["website"];
		}
		
		if (isset($_POST["mode"])) {
			$Ary["mode"] = $_POST["mode"];
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
				die("<script>alert('至少要填写1个作品信息');window.history.back();</script>");
			} else {
				$work[$i]["type"] = $_POST["work".$i]["type"];
			}			
			if (empty($_POST["work".$i]["price"])) {
				die("<script>alert('至少要填写1个作品信息');window.history.back();</script>");
			} else {
				$work[$i]["price"] = $_POST["work".$i]["price"];
			}
		} else {
			die("<script>alert('至少要填写1个作品信息');window.history.back();</script>");
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
			die("<script>alert('提交失败，请再试一次');window.history.back();</script>");
		}
		
		$this->tpl = "index03";
		return true;
	}
}