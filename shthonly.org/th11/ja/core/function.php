<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
function readJson($filename, $mode = 0) {
	$REG = &register::load();
	$filename = $REG->CFG("jsonPath").$filename.".json";
	if (file_exists($filename) && is_readable($filename)) {
		return json_decode(file_get_contents($filename), true);
	} else {
		if ($mode == 0) {
			die("缺少必要文件");
		} else {
			return false;
		}
	}
}
function makeJson($Ary, $line = true) {
	$Ary = json_encode($Ary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	if ($line == true) {
		$Ary = "[ {".trim(preg_replace('#\},\{#', "},\n{", preg_replace('#"(\d+)":\{#', "{", $Ary)), "{}")."} ]";
	}
	return $Ary;
}
function is_realarray($Ary) {
	if (is_array($Ary) && !empty($Ary)) {
		return true;
	} else {
		return false;
	}
}
function is_numint($number) {
	if (is_int($number) || ctype_digit($number)) {
		return true;
	} else {
		return false;
	}
}
function is_realint($number, $max = 999999, $min = 1) {
	if (is_numint($number) && $number >= $min && $number <= $max) {
		return true;
	} else {
		return false;
	}
}
function is_realstr($string, $strict = false) {
	if (is_string($string) || is_numeric($string)) {
		if ($strict == false) {
			return true;
		} else {
			if (trim($string) === "") {
				return false;
			} else {
				return true;
			}
		}
	} else {
		return false;
	}
}
//获取IP
function GetIP() {
	if(!empty($_SERVER["HTTP_CLIENT_IP"]))
		$cip = $_SERVER["HTTP_CLIENT_IP"];
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		elseif(!empty($_SERVER["REMOTE_ADDR"]))
		$cip = $_SERVER["REMOTE_ADDR"];
		else
			$cip = "unknown";
			return $cip;
}

//随机数字
function rndint($l = 6) {
	$low = "1".str_repeat("0", $l - 1);
	$high = ("1".str_repeat("0", $l)) - 1;
	return rand($low, $high);
}

//随机字符
function rndstr($l = 16, $salt = "") {
	/*************
	 *@l - length of random string
	 */
	$c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$rand = "";
	if ($salt == "" || $l < 8) {
		for($i=0; $i < $l; $i++) {
			$rand.= $c[rand()%strlen($c)];
		}
	} else {
		$first = substr(md5($salt), 4);
		$end = substr(md5($salt), -4);
		for($i=0; $i < ($l - 8); $i++) {
			$rand.= $c[rand()%strlen($c)];
		}
		$rand = $first.$rand.$end;
	}
	return $rand;
}

//动态令牌
function token($step, $l = 16) {
	$_SESSION["token_".$step] = "";
	/*************
	 *@l - length of random string
	 */
	$c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$rand = "";
	for($i=0; $i<$l; $i++) {
		$rand.= $c[rand()%strlen($c)];
	}
	$_SESSION["token_".$step] = $rand;
	return $rand;
}
function urlencode_new($str) {
	$temp = preg_replace_callback(
			"/([^\w\/\?\.=:,&@|]*)/",
			function ($matches) {
				return urlencode($matches[1]);
			},
			$str
			);
	return $temp;
}
//过滤字符串
function filter($word, $mod = 1) {
	if ($mod == 1) {
		$word = str_replace(array("\r\n", "\r", "\n", "\\", "`"), '', $word);
		$word = htmlspecialchars(trim($word, "\x00..\x1F"), ENT_QUOTES, 'UTF-8');
		$word = str_replace("&#039;", "\'", $word);
	} elseif ($mod == 2) {
		$word = str_replace(array("\r\n", "\r", "\n", "/", "\\", "`"), '', $word);
	} elseif ($mod == 3) {
		$word = str_replace(array("&#039;", "\\", "'", "`"), '', $word);
		$word = htmlspecialchars(trim($word, "\x00..\x1F"), ENT_QUOTES, 'UTF-8');
	} elseif ($mod == 4) {
		$word = str_replace(array("\r\n", "\r", "\n"), '<br>', $word);
		$word = str_replace(array("&#039;", "\\", "`"), '', $word);
		$word = htmlspecialchars(trim($word, "\x00..\x1F"), ENT_QUOTES, 'UTF-8');
		$word = addslashes($word);
		$word = str_replace(array("\\\\"), '', $word);
	} elseif ($mod == "SQL") {
		$word = str_replace(array("\\"), '', $word);
		$word = str_replace(array("\\\\"), '', $word);
		$word = str_replace(array("\'"), "'", $word);
		$word = str_replace(array("`"), "\`", $word);
		$word = str_replace(array("'"), "\'", $word);
		$word = str_replace("&#039;", "\'", $word);
		$word = trim($word, "\x00..\x1F");
	} else {
		$word = str_replace(array("\r\n", "\r", "\n", "/", "\\", "`"), '', $word);
		$word = addslashes($word);
		$word = str_replace(array("\\\\"), '', $word);
		$word = trim($word, "\x00..\x1F");
	}
	return $word;
}

//文本过滤和替换
function textFilter($text) {
	$text = str_replace(array("<br/>", "<br />"), '<br>', $text);
	$text = str_replace(array("<br>\r\n", "<br>\r", "<br>\n"), '<br>', $text);
	$text = str_replace(array("\r\n", "\r", "\n"), '<br>', $text);
	$text = str_replace(
			array("<b>", "</b>", "<i>", "</i>", "<u>", "</u>", "<s>", "</s>", "<ul>", "</ul>", "<ol>", "</ol>", "<li>", "</li>"),
			array("[b]", "[!b]", "[i]", "[!i]", "[u]", "[!u]", "[s]", "[!s]", "[ul]", "[!ul]", "[ol]", "[!ol]", "[li]", "[!li]"),
			$text);
	$text = str_replace(array("<br>", "<br >"), '[br]', $text);
	$text = preg_replace(
			'#<a (.*)>(.*)</a>#',
			'[a \1]\2[!a]',
			$text);
	$text = preg_replace(
			'#<font (.*)>(.*)</font>#',
			'[font \1]\2[!font]',
			$text);
	$text = filter($text, 3);
	$text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
	$text = str_replace("&amp;", "&", $text);
	$text = preg_replace(
			'#\[font (.*)\](.*)\[!font\]#',
			'<font \1>\2</font>',
			$text);
	$text = preg_replace(
			'#\[a (.*)\](.*)\[!a\]#',
			'<a \1>\2</a>',
			$text);
	$text = str_replace(
			array("[br]", "[b]", "[!b]", "[i]", "[!i]", "[u]", "[!u]", "[s]", "[!s]", "[ul]", "[!ul]", "[ol]", "[!ol]", "[li]", "[!li]"),
			array("<br />", "<b>", "</b>", "<i>", "</i>", "<u>", "</u>", "<s>", "</s>", "<ul>", "</ul>", "<ol>", "</ol>", "<li>", "</li>"),
			$text);
	$text = str_replace('\"', '"', $text);
	return $text;
}