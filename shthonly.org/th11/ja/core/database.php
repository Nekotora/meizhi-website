<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
//数据库类
class dbClass {
	const __CAN__DELETE = true;
	const __USE__ENABLE = false;
	static public $canDB = 0;
	static public $link;
	protected $table = "";
	protected $class = "";
	protected $query = "";
	protected $action = "";
	protected $result = false;
	protected $success = false;
	protected $line = 0;	//目前在结果集的位置，从0开始
	public  $id = 0;		//插入的自动ID或绑定的ID
	protected $affect = -1;//影响的行数
	protected $row = 0;	//结果集的行数
	protected $field = 0;	//结果集的字段数
	protected $start = true;
	protected $end = false;
	protected $error = NULL;
	protected $errno = 0;
	protected $REG;
	protected $prefix = "";
	protected $trans = false;
	protected $errList = [];
	public $Ary = array();
	function __construct($prefix = "") {
		if (self::$canDB != 1) {
			require_once(_BASE."/config_db.php");
			$link = mysqli_connect($mysql_host, $mysql_user, $mysql_pwd, $mysql_dbname);
			if($link != true) {
				self::$canDB = 0;
				self::$link = false;
				echo "无法连接到数据库。<br>";
				exit();
			} else {
				self::$canDB = 1;
				self::$link = $link;
			}
		}
		if ($prefix == "") {
			if (isset($mysql_prefix)) {
				$this->prefix = $mysql_prefix;
			}
		} else {
			$this->prefix = $prefix;
		}
		if (is_string($this->table) && $this->table !== "") {
			$this->class = $this->table;
			$this->setTable($this->table, $this->prefix);
		}
		$this->REG = &register::load();
	}
	function __toString() {
		if ($this->query == "")
			return false;
			return $this->query;
	}
	function __invoke($query = "", $run = false) {
		if ($query != "") {
			if (!is_realstr($query))
				return false;
				if ($run) {
					return $this->query($query);
				} else {
					$this->resetAll(true);
					$this->query = $query;
					return true;
				}
		} else {
			if ($this->query != "") {
				return $this->query(true);
			} else {
				return false;
			}
		}
	}
	function showError() {
		return mysqli_error(self::$link);
	}
	protected function querytime($query = false) {
		if ($query !== false && $query != $this->query) {
			if (isset($_SESSION["querydirecttimes"])) {
				$_SESSION["querydirecttimes"]++;
			} else {
				$_SESSION["querydirecttimes"] = 1;
			}
			varecho("执行了直接查询：".($query?$query:$this->query));
			return true;
		}
		if ($this->success == true) {
			if (isset($_SESSION["querytimes"])) {
				$_SESSION["querytimes"]++;
			} else {
				$_SESSION["querytimes"] = 1;
			}
			varecho("执行了查询：".($query?$query:$this->query));
		} else {
			if (isset($_SESSION["queryfailedtimes"])) {
				$_SESSION["queryfailedtimes"]++;
			} else {
				$_SESSION["queryfailedtimes"] = 1;
			}
			varecho("执行了查询：".($query?$query:$this->query));
		}
	}
	protected function query($query = true) {
		/** 通用SQL执行 **/
		if ($query === true || empty($query) || !is_realstr($query)) {
			$query = $this->query;
		} else {
			$this->query = $query;
		}
		mysqli_query(self::$link, "SET NAMES utf8mb4");
		$this->result = mysqli_query(self::$link, $query);
		$this->action = strtoupper(explode(" ", $query)[0]);
		if ($this->result) {
			$this->success = true;
			$this->field = mysqli_field_count(self::$link);
			if ($this->field == 0) {
				$this->affect = mysqli_affected_rows(self::$link);
				$this->line = 0;
				if ($this->action == "INSERT") {
					$this->id = mysqli_insert_id(self::$link);
				} else {
					$this->id = 0;
				}
				$this->row = 0;
			} else {
				$this->row = mysqli_num_rows($this->result);
				$this->affect = -1;
				$this->line = 0;
				$this->id = 0;
				if ($this->action == "SELECT") {
					if ($this->row == 1) {
						$this->id = $this->getID();
					}
				}
			}
			$this->start = true;
			$this->end = false;
			$this->error = NULL;
			$this->errno = 0;
			$this->Ary = array();
			$this->querytime();
			if ($this->trans != true) {
				$this->errList = [];
			}
			return true;
		} else {
			$this->success = false;
			$this->error = mysqli_error(self::$link);
			$this->errno = mysqli_errno(self::$link);
			$this->resetValue();
			$this->querytime();
			if ($this->trans == true) {
				$this->errList[] = [];
				$this->errList[][0] = $query;
				$this->errList[][1] = $this->errno;
				$this->errList[][2] = $this->error;
			} else {
				$this->errList = [];
			}
			return false;
		}
	}
	public function resetValue() {
		$this->row = 0;
		$this->id = 0;
		$this->affect = -1;
		$this->line = 0;
		$this->field = 0;
		$this->start = true;
		$this->end = false;
		$this->Ary = array();
	}
	public function resetAll($table = false) {
		if ($table == true) {
			$this->table = "";
			$this->prefix = "";
		}
		@mysqli_free_result($this->result);
		$this->query = "";
		$this->action = "";
		$this->result = false;
		$this->success = false;
		$this->row = 0;
		$this->id = 0;
		$this->affect = -1;
		$this->line = 0;
		$this->field = 0;
		$this->start = true;
		$this->end = false;
		$this->error = NULL;
		$this->errno = 0;
		$this->Ary = array();
		$this->trans = false;
		$this->errList = [];
	}
	public function setTable($table, $prefix = false) {
		if (!is_string($table) || empty($table))
			return false;
			if ($this->trans == true) {
				$trans = true;
			} else {
				$trans = false;
			}
			if (!is_string($prefix) || $prefix === false) {
				if ($this->class != $table) {
					$this->resetAll();
					if ($this->prefix != "") {
						$this->table = $this->prefix."_".$table;
					} else {
						$this->table = $table;
					}
					$this->class = $table;
				}
			} elseif ($prefix === "") {
				if ($this->table != $table) {
					$this->resetAll();
					$this->table = $table;
					$this->class = $table;
				}
			} else {
				$this->prefix = $prefix;
				if ($this->table != $this->prefix."_".$table) {
					$this->resetAll();
					$this->table = $this->prefix."_".$table;
					$this->class = $table;
				}
			}
			$this->trans = $trans;
			return $this->table;
	}
	public function setPrefix($prefix) {
		if (!is_realstr($prefix))
			return false;
			$this->prefix = $prefix;
			$this->setTable($this->class, $this->prefix);
			return $this->table;
	}
	public function setID($id, $reset = false) {
		if (!is_numint($id))
			return false;
			if ($reset === true) {
				$this->resetAll();
			}
			$this->id = $id;
	}
	public function getInsertID() {
		if ($this->action != "INSERT" || $this->id == 0)
			return false;
			return $this->id;
	}
	public function getFields() {
		if ($this->action == "")
			return false;
			if ($this->field == 0)
				return NULL;
				return $this->field;
	}
	public function getAction() {
		if ($this->action == "")
			return false;
			return $this->action;
	}
	public function getRows() {
		if ($this->action != "SELECT")
			return false;
			if ($this->row == 0)
				return 0;
				return $this->row;
	}
	public function getAffect() {
		if ($this->action == "SELECT" || $this->affect == -1)
			return false;
			return $this->affect;
	}
	public function error($type = 0) {
		if ($this->errno == 0) {
			if ($type == 0) {
				return $this->errno;
			} else {
				return $this->errno . " " . $this->error;
			}
		}
	}
	public function get($store = false, $type = "assoc") {
		/** 获取接下来一条 **/
		if ($this->success == true && $this->row > 0 && $this->end != true) {
			if ($this->line == $this->row) {
				$this->end == true;
			}
			$this->start = false;
			$this->line++;
			if ($store === false) {
				if ($type == "assoc") {
					return mysqli_fetch_assoc($this->result);
				} elseif ($type == "num") {
					return mysqli_fetch_row($this->result);
				} elseif ($type == "object") {
					return mysqli_fetch_object($this->result);
				} else {
					mysqli_data_seek($this->result, $this->line);
					return true;
				}
			} else {
				if ($type == "assoc") {
					return $this->Ary = mysqli_fetch_assoc($this->result);
				} elseif ($type == "num") {
					return $this->Ary = mysqli_fetch_row($this->result);
				} elseif ($type == "object") {
					return $this->Ary = mysqli_fetch_object($this->result);
				} else {
					mysqli_data_seek($this->result, $this->line);
					return true;
				}
			}
		} else {
			return false;
		}
	}
	public function getID() {
		/** 获取接下来一条的ID，并且不移动指针 **/
		if ($this->success == true && $this->row > 0) {
			$temp = mysqli_fetch_assoc($this->result);
			mysqli_data_seek($this->result, $this->line);
			if ($temp == NULL)
				return NULL;
				if (isset($temp["id"])) {
					$this->id = $temp["id"];
					return $temp["id"];
				} else {
					return -1;
				}
		} else {
			return false;
		}
	}
	public function getFirst($store = false, $type = "assoc") {
		/** 获取第一条 **/
		if ($this->success == true && $this->row > 0) {
			mysqli_data_seek($this->result, 0);
			if ($type == "assoc") {
				$temp = mysqli_fetch_assoc($this->result);
			} elseif ($type == "num") {
				$temp = mysqli_fetch_row($this->result);
			} elseif ($type == "object") {
				$temp = mysqli_fetch_object($this->result);
			}
			if ($store === true) {
				$this->Ary = $temp;
			}
			mysqli_data_seek($this->result, $this->line);
			return $temp;
		} else {
			return false;
		}
	}
	public function getLastID() {
		$temp = $this->getFirst();
		if (isset($temp["id"])) {
			$this->id = $temp["id"];
			return true;
		} else {
			return false;
		}
	}
	public function getLast($store = false, $type = "assoc") {
		/** 获取最后一条 **/
		if ($this->success == true && $this->row > 0) {
			mysqli_data_seek($this->result, $this->row - 1);
			if ($type == "assoc") {
				$temp = mysqli_fetch_assoc($this->result);
			} elseif ($type == "num") {
				$temp = mysqli_fetch_row($this->result);
			} elseif ($type == "object") {
				$temp = mysqli_fetch_object($this->result);
			}
			if ($store === true) {
				$this->Ary = $temp;
			}
			mysqli_data_seek($this->result, $this->line);
			return $temp;
		} else {
			return false;
		}
	}
	public function getAll($store = false, $type = "assoc") {
		/** 获取全部 **/
		if ($this->success == true && $this->row > 0) {
			mysqli_data_seek($this->result, 0);
			$temp = array();
			while ($temprow = mysqli_fetch_assoc($this->result)) {
				$temp[] = $temprow;
			}
			if ($store === true) {
				$this->Ary = $temp;
			}
			mysqli_data_seek($this->result, $this->line);
			return $temp;
		} else {
			return false;
		}
	}
	public function setSeek($location = 0) {
		/** 设置MySQL指针 **/
		if ($this->success == true && $this->row > 0) {
			if (!is_numint($location)) {
				return false;
			}
			mysqli_data_seek($this->result, $location);
			$this->line = $location;
		} else {
			return false;
		}
	}
	public function resetSeek() {
		/** 设置MySQL指针 **/
		if ($this->success == true && $this->row > 0) {
			mysqli_data_seek($this->result, 0);
			$this->line = 0;
		} else {
			return false;
		}
	}
	public function selectAll($target = true, $all = true) {
		$this->select(true, $target);
		if ($all == true) {
			return $this->getAll();
		} else {
			return $this->get();
		}
	}
	public function selectID($id = true, $target = true) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, true, ["id" => $id]);
		if ($target == false) {
			return true;
		}
		return $this->get();
	}
	public function selectDelID($id = true, $target = true) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, true, ["id" => $id], false, false, false, false, false, true);
		if ($target == false) {
			return true;
		}
		return $this->get();
	}
	public function selectField($field, $value, $all = false, $target = true) {
		if (!is_realstr($field) || !is_realstr($value))
			return false;
			if ($target != true && $target != false) {
				$f = $target;
			} else {
				$f = true;
			}
			$this->select(true, $f, [$field => $value]);
			if ($all == false) {
				if ($target == false) {
					return true;
				}
				return $this->get();
			} else {
				return $this->getAll();
			}
	}
	public function selectField2($field, $value, $field2, $value2, $all = false, $target = true) {
		if (!is_realstr($field) || !is_realstr($value) || !is_realstr($field2) || !is_realstr($value2))
			return false;
			if ($target != true && $target != false) {
				$f = $target;
			} else {
				$f = true;
			}
			$this->select(true, $f, [$field => $value, $field2 => $value2]);
			if ($all == false) {
				if ($target == false) {
					return true;
				}
				return $this->get();
			} else {
				return $this->getAll();
			}
	}
	public function selectField3($field, $value, $field2, $value2, $field3, $value3, $all = false, $target = true) {
		if (!is_realstr($field) || !is_realstr($value) || !is_realstr($field2) || !is_realstr($value2) || !is_realstr($field3) || !is_realstr($value3))
			return false;
			if ($target != true && $target != false) {
				$f = $target;
			} else {
				$f = true;
			}
			$this->select(true, $f, [$field => $value, $field2 => $value2, $field3 => $value3]);
			if ($all == false) {
				if ($target == false) {
					return true;
				}
				return $this->get();
			} else {
				return $this->getAll();
			}
	}
	public function selectFieldByID($field, $id = true, $default= NULL) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, $field, ["id" => $this->id]);
		if ($this->row > 0) {
			if (is_realstr($field)) {
				return $this->getFirst()[$field];
			} else {
				return $this->getFirst();
			}
		} else {
			return $default;
		}
	}
	public function selectFieldByDelID($field, $id = true, $default= NULL) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, $field, ["id" => $this->id], false, false, false, false, false, true);
		if ($this->row > 0) {
			if (is_realstr($field)) {
				return $this->getFirst()[$field];
			} else {
				return $this->getFirst();
			}
		} else {
			return $default;
		}
	}
	public function checkIDExist($id = true) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, "id", ["id" => $id]);
		return ($this->row != 0?true:false);
	}
	public function checkDelIDExist($id = true, $null = 0) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		$this->select(true, ["id", "enable"], ["id" => $id], false, false, false, false, false, true);
		$temp = $this->get();
		return ($temp["enable"] != $null?true:false);
	}
	public function checkFieldExist($field, $value) {
		$this->select(true, "id", [$field => $value]);
		return ($this->row != 0?true:false);
	}
	public function checkField2Exist($field, $value, $field2, $value2) {
		$this->select(true, "id", [$field => $value, $field2 => $value2]);
		return ($this->row != 0?true:false);
	}
	public function getFuncID($func, $condition = "") {
		$this->select(true, ["id" => ["func" => $func, "AS" => "i"]], $condition);
		if ($this->row > 0) {
			return $this->getFirst(	)["i"];
		} else {
			return NULL;
		}
	}
	public function addOneByID($id = true, $field) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		return $this->update(true, [$field => ["field" => $field]], ["id" => $id]);
	}
	public function subOneByID($id = true, $field) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		return $this->update(true, [$field => ["field" => $field, "operator" => "-", "value" => "1"]], ["id" => $id]);
	}
	public function updateByID($id = true, $Ary) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		return $this->update(true, $Ary, ["id" => $id]);
	}
	public function updateByDelID($id = true, $Ary) {
		if ($id === true || $id === $this->id) {
			if ($this->id == 0)
				return false;
				$id = $this->id;
		} else {
			if (!is_numint($id)) {
				return false;
			}
			$this->setID($id);
		}
		return $this->update(true, $Ary, ["id" => $id], false, true);
	}
	public function updateByField($field, $value, $Ary) {
		if (!is_realstr($field, true) || !is_realstr($value, true)) {
			return false;
		}
		return $this->update(true, $Ary, [$field => $value]);
	}
	public function updateByField2($field, $value, $field2, $value2, $Ary) {
		if (!is_realstr($field, true) || !is_realstr($value, true) || !is_realstr($field2, true) || !is_realstr($value2, true)) {
			return false;
		}
		return $this->update(true, $Ary, [$field => $value, $field2 => $value2]);
	}
	public function fieldExpr($field) {
		if ($field === true || (is_array($field) && empty($field))) {
			$field = "*";
		} elseif (is_realstr($field)) {
			$field = "`$field`";
		} elseif (is_array($field)) {
			$temp = "";
			$len = count($field);
			$i = 1;
			foreach ($field as $key => $value) {
				if (!is_array($value) && is_integer($key)) {
					$temp .= "`$value`";
					if ($i < $len)
						$temp .= ", ";
						$i++;
				} elseif (!is_array($value)) {
					$temp .= "`$key`";
					if ($value != "")
						$temp .= " AS `$value`";
						if ($i < $len)
							$temp .= ", ";
							$i++;
				} else {
					if (!empty($value["table"])) {
						$key = "".$value["table"]."`.`$key";
					}
					if ($value["func"] != "") {
						if (!empty($value["DISTINCT"])) {
							$temp .= $value["func"]."(DISTINCT ".($key == "*" ? $key : "`$key`" ).")";
						} else {
							$temp .= $value["func"]."(".($key == "*" ? $key : "`$key`" ).")";
						}
					} else {
						$temp .= "`$key`";
					}
					if ($value["AS"] != "")
						$temp .= " AS `".$value["AS"]."`";
						if ($i < $len)
							$temp .= ", ";
							$i++;
				}
			}
			$field = $temp;
		} else {
			return false;
		}
		return $field;
	}
	public function select($table = true, $field = true, $condition = "", $limit = false, $distinct = false, $group = false, $order = false, $show = false, $revive = false) {
		if ($table === true) {
			if (empty($this->table)) {
				return false;
			}
			$table = $this->table;
		} else {
			if (!is_realstr($table)) {
				if (is_array($table) && !empty($table)) {
					$temp = "";
					$len = count($table);
					$i = 1;
					foreach ($table as $value) {
						$temp .= "`$value`";
						if ($i < $len)
							$temp .= ",";
							$i++;
					}
					$table = $temp;
				} else {
					return false;
				}
			} else {
				if ($this->prefix != "") {
					$table = "`".$this->prefix."_".$table."`";
				} else {
					$table = "`".$table."`";
				}
			}
		}
		$field = $this->fieldExpr($field);
		if ($field == false) return false;
		$condition = $this->expr($condition);
		if ($condition == false || $condition === true) {
			$condition = "";
		} else {
			if (self::__USE__ENABLE == true && $revive == false) {
				$condition = "( ".$condition." ) AND `enable` = 1 ";
			}
			$condition = "WHERE ".$condition;
		}
		if (is_array($limit) && count($limit) >= 1) {
			if (count($limit) == 1) {
				$limit = " LIMIT ".$limit;
			} else {
				$limit = " LIMIT ".$limit[0].", ".$limit[1];
			}
		} elseif (is_numint($limit)) {
			$limit = " LIMIT ".$limit;
		}
		if ($distinct == true) {
			$distinct = "DISTINCT";
		} else {
			$distinct = "";
		}
		if ($order != false) {
			if (!is_array($order) && is_realstr($order)) {
				$order = " ORDER BY `$order`";
			} elseif (is_array($order) && !empty($order)) {
				$temp = " ORDER BY ";
				$len = count($order);
				$i = 1;
				foreach ($order as $key => $value) {
					if ($value !== "ASC") {
						$value = "DESC";
					}
					$temp .= "`$key` $value";
					if ($i < $len)
						$temp .= ", ";
						$i++;
				}
				$order = $temp;
			} else {
				$order = "";
			}
		} else {
			$order = "";
		}
		if ($group != false) {
			if (!is_array($group) && is_realstr($group)) {
				$group = " GROUP BY `$group`";
			} elseif (is_array($group) && isset($group["field"])) {
				if (isset($group["having"]) && isset($group["value"]) && isset($group["operator"])) {
					$group["operator"] = (is_realstr($group["operator"], true)?$group["operator"]:"=");
					$group["having"] = (is_realstr($group["having"], true)?$group["having"]:$group["field"]);
					if (isset($group["func"]) && is_realstr($group["func"], true)) {
						$group = " GROUP BY `".$group["field"]."` HAVING ".$group["func"]."(".$group["having"].") ".$group["operator"]." ".$group["value"];
					} else {
						$group = " GROUP BY `".$group["field"]."` HAVING ".$group["having"]." ".$group["operator"]." ".$group["value"];
					}
				} else {
					$group = " GROUP BY `".$group["field"]."`";
				}
			} else {
				$group = "";
			}
		} else {
			$group = "";
		}
		$query = "SELECT $distinct $field FROM $table $condition $group $order $limit";
		if ($show == false) {
			return $this->query($query);
		} else {
			return $this->query = $query;
		}
	}
	public function select_join($table = true, $prefix, $field = "", $tabAry = "", $onAry = "", $whereAry = "", $show = false) {
		if ($table === true) {
			if (empty($this->table)) return false;
			$table = $this->table;
		} else {
			if (!is_realstr($table)) return false;
			if ($this->prefix != "") $table = $this->prefix."_".$table;
		}
		if (!is_array($tabAry) || count($tabAry) == 0) return false;
		if (!is_array($onAry) || count($onAry) != count($tabAry)) return false;
		$field = $this->fieldExpr($field);
		$query = "SELECT $field FROM `".$table."`";
		foreach ($tabAry as $key => $tab) {
			if (!is_realstr($tab)) return false;
			if ($prefix == true) $tab = $this->prefix."_".$tab;
			$ASstr = "a".$key;
			if ($tab == $table) {
				$AS = true;
			} else {
				$AS = false;
			}
			$condition = $this->expr($onAry[$key], false);
			if ($condition == false || $condition === true) {
				$condition = "";
			} else {
				if (self::__USE__ENABLE == true) {
					$condition = "( ".$condition." ) AND `enable` = 1 ";
				}
			}
			if (!empty($condition)) {
				$query .= " INNER JOIN `".$tab."`".($AS ? " AS `".$ASstr."`" : "")." ON $condition ";
			} else {
				$query .= " INNER JOIN `".$tab."`".($AS ? " AS `".$ASstr."`" : "")." ";
			}
		}
		$condition = $this->expr($whereAry, false);
		if ($condition == false || $condition === true) {
			$condition = "";
		} else {
			if (self::__USE__ENABLE == true) {
				$condition = "( ".$condition." ) AND `enable` = 1 ";
			}
			$condition = "WHERE ".$condition;
		}
		$query .= " $condition";
		if ($show == false) {
			return $this->query($query);
		} else {
			return $this->query = $query;
		}
	}
	public function insert($table = true, $Ary = "", $show = false) {
		if ($table === true) {
			if (empty($this->table)) {
				return false;
			}
			$table = $this->table;
		} else {
			if (!is_realstr($table)) {
				return false;
			}
			if ($this->prefix != "") {
				$table = $this->prefix."_".$table;
			}
		}
		if (!is_array($Ary) || count($Ary) == 0) {
			return false;
		}
		$query = "INSERT INTO `".$table."` (`";
		$i = 0;
		$arr_n = count($Ary) - 1;
		foreach ($Ary as $key => $value) {
			if ($arr_n != $i) {
				$query .= $key."`, `";
			} else {
				$query .= $key."`) VALUES (";
			}
			$i++;
		}
		$i = 0;
		foreach ($Ary as $key => $value) {
			if ($arr_n != $i) {
				$query .= "'".$value."', ";
			} else {
				$query .= "'".$value."')";
			}
			$i++;
		}
		if ($show == false) {
			return $this->query($query);
		} else {
			return $this->query = $query;
		}
	}
	public function update($table = true, $Ary = "", $condition, $show = false, $revive = false) {
		if ($table === true) {
			if (empty($this->table)) {
				return false;
			}
			$table = $this->table;
		} else {
			if (!is_realstr($table)) {
				return false;
			}
			if ($this->prefix != "") {
				$table = $this->prefix."_".$table;
			}
		}
		if (!is_array($Ary) || count($Ary) == 0) {
			return false;
		}
		$query = "UPDATE `".$table."` SET `";
		$i = 0;
		$arr_n = count($Ary) - 1;
		foreach ($Ary as $key => $value) {
			if (!is_array($value) || count($value) == 0) {
				$query .= $key."` = '".$value."'";
			} else {
				$query .= $key."` = ";
				if (isset($value["func"])) {
					$query .= $value["func"]."(".$value["param"].") ";
				} elseif (isset($value["field"])) {
					$query .= "`".$value["field"]."` ";
				}
				if (!isset($value["operator"]))
					$value["operator"] = "+";
					if (!isset($value["value"]))
						$value["value"] = "1";
						$query .= " ".$value["operator"]." ".$value["value"]."";
			}
			if ($arr_n != $i) {
				$query .= ", `";
			}
			$i++;
		}
		$condition = $this->expr($condition);
		if ($condition == false)
			return false;
			if (self::__USE__ENABLE == true && $revive == false) {
				$condition = "( ".$condition." ) AND `enable` = 1 ";
			}
			$query .= " WHERE ".$condition;
			if ($show == false) {
				return $this->query($query);
			} else {
				return $this->query = $query;
			}
	}
	public function delete($table = true, $condition, $show = false) {
		if (self::__CAN__DELETE == false) {
			return false;
		}
		if ($table === true) {
			if (empty($this->table)) {
				return false;
			}
			$table = $this->table;
		} else {
			if (!is_realstr($table)) {
				return false;
			}
			if ($this->prefix != "") {
				$table = $this->prefix."_".$table;
			}
		}
		$condition = $this->expr($condition);
		if ($condition == false)
			return false;
			$query = "DELETE FROM `$table` WHERE $condition";
			if ($show == false) {
				return $this->query($query);
			} else {
				return $this->query = $query;
			}
	}
	public function truncate($table, $prefix = "", $show = false) {
		if (self::__CAN__DELETE == false) {
			return false;
		}
		if ($table == "") {
			return false;
		} else {
			if (!is_realstr($table)) {
				return false;
			}
		}
		if ($prefix == "") {
			$prefix = $this->prefix;
		}
		if ($prefix != "") {
			$query = "TRUNCATE TABLE `$prefix"."_"."$table`";
		} else {
			$query = "TRUNCATE TABLE `$table`";
		}
		if ($show == false) {
			return $this->query($query);
		} else {
			return $this->query = $query;
		}
	}
	public function trans_begin() {
		return $this->transaction("begin");
	}
	public function trans_commit($rollback = false) {
		if ($rollback == false) {
			return $this->transaction("commit");
		} else {
			return $this->transaction("rollback");
		}
	}
	public function trans_end($rollback = false) {
		if ($rollback == false) {
			return $this->transaction("end");
		} else {
			$r1 = $this->transaction("rollback");
			if ($r1) {
				$r2 = mysqli_autocommit(self::$link, true);
				varecho($r2?">>事务结束<<<br />\n":">>事务结束失败<<<br />\n");
				if ($r2) {
					return $r1;
				} else {
					return -1;
				}
			} else {
				return -1;
			}
		}
	}
	public function transaction($status) {
		if ($status == "begin") {
			if ($this->trans == true) {
				varecho("已经开启了事务<br />\n");
				return false;
			}
			$result = mysqli_autocommit(self::$link, false);
			if ($result) $this->trans = true;
			varecho($result?">>事务开始<<<br />\n":">>事务开始失败<<<br />\n");
			return $result;
		} elseif ($status == "commit") {
			if ($this->trans == false) {
				varecho("没有事务可以提交<br />\n");
				return false;
			}
			if (count($this->errList) == 0) {
				$result = mysqli_commit(self::$link);
				varecho($result?">>事务提交<<<br />\n":">>事务提交失败<<<br />\n");
				$r = true;
			} else {
				$result = mysqli_rollback(self::$link);
				varecho($result?">>事务回滚<<<br />\n":">>事务回滚失败<<<br />\n");
				vardump($this->errList);
				$this->errList = [];
				$r = false;
			}
			return $r;
		} elseif ($status == "rollback") {
			if ($this->trans == false) {
				varecho("没有事务可以回滚<br />\n");
				return false;
			}
			$result = mysqli_rollback(self::$link);
			$this->errList = [];
			varecho($result?">>事务回滚<<<br />\n":">>事务回滚失败<<br />\n<");
			return $result;
		} elseif ($status == "end") {
			if ($this->trans == false) {
				varecho("没有事务可以结束<br />\n");
				return false;
			}
			if (count($this->errList) == 0) {
				$result = mysqli_commit(self::$link);
				varecho($result?">>事务提交<<<br />\n":">>事务提交失败<<<br />\n");
				$r = 1;
			} else {
				$result = mysqli_rollback(self::$link);
				varecho($result?">>事务回滚<<<br />\n":">>事务回滚失败<<<br />\n");
				vardump($this->errList);
				$this->errList = [];
				$r = 0;
			}
			if ($result) {
				$this->trans = false;
				$r2 = mysqli_autocommit(self::$link, true);
				varecho($r2?">>事务结束<<<br />\n":">>事务结束失败<<<br />\n");
				if ($r2) {
					return $r;
				} else {
					return -1;
				}
			} else {
				return -1;
			}
		}
	}
	public function showlist() {
		$query = "SHOW FULL PROCESSLIST";
		mysqli_query(self::$link, "SET NAMES utf8mb4");
		$result = mysqli_query(self::$link, $query);
		self::querytime($query);
		return $this->getAll();
	}
	public function expr($Ary = array(), $filter = true) {
		if (empty($Ary)) return "";
		if ($Ary === true || $Ary === false) {
			return "";
		}
		$result = array();
		$n = 0;
		foreach ($Ary as $field => $word) {
			if (is_array($word)) {
				if (is_integer($field) && count($word) == 1) {
					$field = key($word);
					$word = $word[$field];
				}
				if (!isset($word["value"]))
					$word["value"] = "";
					$result["logic".$n] = (!empty($word["logic"])?$word["logic"]:"AND");
					$result["level".$n] = (!empty($word["level"])?$word["level"]:0);
					if (!empty($word["table"])) {
						$result["c".$n] = (empty($word["field"])) ? "`".$word["table"]."`.`$field` " : "`".$word["table"]."`.`".$word["field"]."` ";
					} else {
						$result["c".$n] = (empty($word["field"])) ? "`$field` " : "`".$word["field"]."` ";
					}
					$operator = (isset($word["operator"])?$word["operator"]:"=");
					if ($operator == "BETWEEN") {
						if (!is_array($word["value"]) || count($word["value"]) != 2)
							return false;
							$result["c".$n] .= "BETWEEN '".filter($word["value"][0], "SQL")."' AND '".filter($word["value"][1], "SQL")."'";
					} elseif ($operator == "IN") {
						if (!is_array($word["value"]) || empty($word["value"]))
							return false;
							$result["c".$n] .= "IN(";
							$len = count($word["value"]);
							$i = 0;
							foreach ($word["value"] as $value) {
								$result["c".$n] .= (empty($word["table"])) ? "'".filter($value, "SQL")."'" : filter($value, "SQL");
								$i++;
								if ($i != $len) {
									$result["c".$n] .= ", ";
								}
							}
							$result["c".$n] .= ")";
					} elseif ($operator == "LIKE") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "LIKE '".filter($word["value"], "SQL")."'";
					} elseif ($operator == "LIKE%") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "LIKE '".filter($word["value"], "SQL")."%'";
					} elseif ($operator == "%LIKE") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "LIKE '%".filter($word["value"], "SQL")."'";
					} elseif ($operator == "%LIKE%") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "LIKE '%".filter($word["value"], "SQL")."%'";
					} elseif ($operator == "NOTLIKE") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "NOT LIKE '".filter($word["value"], "SQL")."'";
					} elseif ($operator == "NOTLIKE%") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "NOT LIKE '".filter($word["value"], "SQL")."%'";
					} elseif ($operator == "%NOTLIKE") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "NOT LIKE '%".filter($word["value"], "SQL")."'";
					} elseif ($operator == "%NOTLIKE%") {
						if (!is_realstr($word["value"]))
							return false;
							$result["c".$n] .= "NOT LIKE '%".filter($word["value"], "SQL")."%'";
					} elseif ($operator == "NULL") {
						$result["c".$n] .= "IS NULL";
					} else {
						$word["quote"] = true;
						if (!is_realstr($word["value"])) {
							if (is_array($word["value"]) && !empty($word["value"]["value"])) {
								if (!empty($word["value"]["table"])) {
									$word["value"] = "`".$word["value"]["table"]."`.`".$word["value"]["value"]."`";
									$word["quote"] = false;
								} else {
									$word["value"] = $word["value"]["value"];
									$word["quote"] = true;
								}
							} else {
								return false;
							}
						}
						if (isset($word["func"]) && $word["func"] == true) {
							$result["c".$n] .= "$operator ".$word["func"]."(".(($filter == true)?filter(filter($word["value"])):$word["value"]).")";
						} elseif ($word["quote"] == false) {
							$result["c".$n] .= "$operator ".(($filter == true)?filter(filter($word["value"])):$word["value"]);
						} else {
							$result["c".$n] .= "$operator '".(($filter == true)?filter(filter($word["value"])):$word["value"])."'";
						}
					}
					if (isset($word["not"]) && $word["not"] == true) {
						$result["c".$n] = "NOT(".$result["c".$n].")";
					}
			} else {
				if (!is_realstr($word))
					return false;
					if ($n != 0) {
						$result["logic".$n] = "AND";
					}
					$result["c".$n] = "`$field` = '".(($filter == true)?filter($word):$word)."'";
			}
			$n++;
		}
		$string = "";
		$now = 0;
		$result["logic0"] = "";
		for ($i = 0; $i < $n; $i++) {
			$level = (isset($result["level".$i])?$result["level".$i]:0);
			$logic = (isset($result["logic".$i])?($result["logic".$i]." "):"");
			if ($level > $now) {
				$front = str_repeat("(", $level - $now);
				$string .= " ".$logic.$front.$result["c".$i];
			} elseif ($level < $now) {
				$back = str_repeat(")", $now - $level);
				$string .= $back." ".$logic.$result["c".$i];
			} else {
				$string .= " ".$logic.$result["c".$i];
			}
			$now = $level;
		}
		if ($now > 0) {
			$back = str_repeat(")", $now - 0);
			$string .= $back;
		}
		return $string;
	}
}
$db = new dbClass();
$REG->initDB($db);