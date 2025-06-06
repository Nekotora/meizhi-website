<?php
if ( !defined( "_KEY" ) ) {
	exit("false");
}

if (!isset($lang["times"])) {
	$lang["times"] = $REG->CFG("times");
}
if (!isset($lang["sitetitle"])) {
	$lang["sitetitle"] = "上海東方オンリーイベント";
}
if (!isset($lang["timesitetitle"])) {
	$lang["timesitetitle"] = $lang["times"].$lang["sitetitle"];
}
if (!isset($lang["author"])) {
	$lang["author"] = "上海東方オンリーイベント";
}