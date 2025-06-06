<?php
	if ( !defined( "_KEY" ) ) {
		exit();
	}
	class work extends dbClass {
		protected $table = "work";
		function addWork($Ary) {	
			if (is_array($Ary) && isset($Ary["name"])) {
				foreach ($Ary as &$value) {
					$value = filter($value, "SQL");
				}

				$istAry["name"] = (isset($Ary["name"]) ? $Ary["name"] : "");
				$istAry["type"] = (isset($Ary["type"]) ? $Ary["type"] : "");
				$istAry["price"] = (isset($Ary["price"]) ? $Ary["price"] : "");
				
				$this->insert(true, $istAry);
				$id = $this->getInsertID();
				if ($id > 0) {
					return $id;
				} else {
					return 0;
				}
			} else {
				return false;
			}
		}
	}
	