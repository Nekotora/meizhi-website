<?php
	if ( !defined( "_KEY" ) ) {
		exit();
	}
	class japan extends dbClass {
		protected $table = "japan";
		function addCircle($Ary) {	
			if (is_array($Ary) && isset($Ary["name"])) {
				foreach ($Ary as &$value) {
					$value = filter($value, "SQL");
				}
				if (!is_realint($Ary["space"])) {
					$Ary["space"] = 1;
				}

				$istAry["name"] = (isset($Ary["name"]) ? $Ary["name"] : "");
				$istAry["author"] = (isset($Ary["author"]) ? $Ary["author"] : "");
				$istAry["email"] = (isset($Ary["email"]) ? $Ary["email"] : "");
				$istAry["website"] = (isset($Ary["website"]) ? $Ary["website"] : "");
				$istAry["space"] = (isset($Ary["space"]) ? $Ary["space"] : "");
				$istAry["nearby"] = (isset($Ary["nearby"]) ? $Ary["nearby"] : "");
				$istAry["textarea"] = (isset($Ary["textarea"]) ? $Ary["textarea"] : "");
				$istAry["recaptcha"] = (isset($Ary["recaptcha"]) ? $Ary["recaptcha"] : "");
				
				$istAry["work1"] = (isset($Ary["work1"]) ? $Ary["work1"] : "0");
				$istAry["work2"] = (isset($Ary["work2"]) ? $Ary["work2"] : "0");
				$istAry["work3"] = (isset($Ary["work3"]) ? $Ary["work3"] : "0");
				
				return $this->insert(true, $istAry);
			} else {
				return false;
			}
		}
	}
	