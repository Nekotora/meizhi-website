<?php
$lan = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
if (preg_match("/(?i)^[ja]/", $lan)) {
    $url = 'ja/';
    header("location: " . $url);
} else {
    $url = 'zh/';
    header("location: " . $url);
}