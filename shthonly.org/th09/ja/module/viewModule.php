<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}
class viewModule extends module {
	public $name = "view";
	protected function checkGET() {
		if (isset($_GET["password"]) && $_GET["password"] == "joessrisgod") {
			return "view";
		}
		return "read";
	}
	protected function checkPOST() {
		if ($this->action == "view") {
			return "view";
		}
		return "read";
	}
	protected function view() {
		$circle = _DATABASE;
		$Dc = new $circle();
		$Dw = new work();
		
		$result = $Dc->selectAll();
		
		$table = [];
		if (is_array($result)) {
			foreach ($result as $value) {
				$Ary = [];
				$Ary["id"] = $value["id"];
				$Ary["time"] = $value["time"];
				$Ary["name"] = $value["name"];
				$Ary["author"] = $value["author"];
				$Ary["email"] = $value["email"];
				$Ary["website"] = $value["website"];
				$Ary["nearby"] = $value["nearby"];
				$Ary["space"] = $value["space"];
				
				for ($i = 1; $i <= 3; $i++) {
					if (!empty($value["work".$i])) {
						$work = $Dw->selectID($value["work".$i]);
						if (is_array($work)) {
							$Ary["work".$i] = $work["name"] . " | " . $work["type"] . " | " . $work["price"];
						}
					} else {
						$Ary["work".$i] = "";
					}
				}
				
				$Ary["textarea"] = $value["textarea"];
				$table[] = $Ary;
			}
		}
		$this->lang["list"] = makeJson($table, false);
		
		$this->tpl = "index01";
		return true;
	}
	protected function read() {
		return true;
	}
}