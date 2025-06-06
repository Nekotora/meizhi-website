<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}


function include_easy($filename, &$lang) {
	$REG = &register::load();
	if (!isset($lang["root"])) {
		$lang["root"] = $REG->CFG("root");
	}
	$filename = $REG->CFG("tplPath").$filename;
	if (file_exists($filename) && is_readable ($filename)) {
		require($filename);
	} else {
		if (_SETMODE) {
			die($REG->error(4002)."： $filename");
		} else {
			die($REG->error(4002));
		}
	}
}
function include_safe($filename, &$lang) {
	$REG = &register::load();
	if (!isset($lang["root"])) {
		$lang["root"] = $REG->CFG("root");
	}
	include_once($REG->CFG("langPath"). _LANG .".php");
	if (file_exists($filename) && is_readable ($filename)) {
		require($filename);
	} else {
		if (_SETMODE) {
			die($REG->error(4002)."： $filename");
		} else {
			die($REG->error(4002));
		}
	}
}

function include_ob($filename, &$lang, $die = true) {
	$REG = &register::load();
	if (!isset($lang["root"])) {
		$lang["root"] = $REG->CFG("root");
	}
	include_once($REG->CFG("langPath"). _LANG .".php");
	if (file_exists($filename) && is_readable ($filename)) {
		require($filename);
		ob_flush();
		flush();
		return true;
	} else {
		if ($die == true) {
			if (_SETMODE) {
				die($REG->error(4002)."： $filename");
			} else {
				die($REG->error(4002));
			}
		} else {
			return false;
		}
	}
}