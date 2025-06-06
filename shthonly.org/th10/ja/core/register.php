<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
function class_loader($class) {
	$filename = _CPATH."/".$class.'.class.php';
	if (file_exists($filename)) {
		include $filename;
	}
}
function module_loader($class) {
	$filename = _MPATH."/".$class.'.php';
	if (file_exists($filename)) {
		require $filename;
	}
}
function core_loader($class) {
	$filename = _BASE.'/core/'.$class.'.inc.php';
	if (file_exists($filename)) {
		include $filename;
	}
}
spl_autoload_register('core_loader');
spl_autoload_register('module_loader');
spl_autoload_register('class_loader');

Class register {
	/** 全局注册器 **/
	private $_OBJECT = array();
	private $_DB = 0;
	private $_CONFIG = array();
	private $_ERROR = array();
	private $_VQ = array();
	
	public function initCFG(&$cfg) {
		if (is_array($cfg) || is_object($cfg)) {
			$this->_CONFIG = &$cfg;
			return true;
		} else {
			return false;
		}
	}
	public function CFG($item = false, $value = false) {
		if ($item === false && $value === false) {
			return $this->_CONFIG;
		} elseif ($item !== false && $value === false) {
			return $this->_CONFIG[$item];
		} else {
			$this->_CONFIG[$item] = $value;
		}
	}
	public function initERROR() {
		$filename = $this->CFG("errofile");
		if (file_exists($filename) && is_readable ($filename)) {
			$this->_ERROR = parse_ini_file($filename, false);
		} else {
			die("Can not load BASE FILES.");
		}
	}
	public function error($id, $section = false) {
		if (!is_numint($id)) {
			return false;
		} elseif ($section != false && is_realstr($section)) {
			return $this->_ERROR[substr($id, 0, 1)."000"]." $id ".$this->_ERROR[$id];
			//return $this->_ERROR[$section][$id];
		} else {
			if (array_key_exists($id, $this->_ERROR)) {
				return $this->_ERROR[$id];
			} else {
				return $this->_ERROR[0000];
			}
		}
	}
	public function initDB(&$db) {
		if ($this->_DB === 0) {
			$this->_DB = &$db;
			return true;
		} else {
			return false;
		}
	}
	public function DB() {
		return $this->_DB;
	}
	public function set($name, &$object) {
		if (is_object($object)) {
			$this->_OBJECT[$name] = &$object;
		} else {
			return false;
		}
	}
	public function &get($name) {
		if (isset($this->_OBJECT[$name]) && is_object($this->_OBJECT[$name])) {
			return $this->_OBJECT[$name];
		} else {
			return false;
		}
	}
	public static function &load() {
		static $register;
		if ($register instanceof register) {
			return $register;
		} else {
			$register = new register;
			return $register;
		}
	}
}
/** 进行注册 **/
$REG = &register::load();
$REG->initCFG($cfg);
$REG->initERROR();