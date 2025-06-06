<?php
$lan = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
if (preg_match("/(?i)^[ja]/", $lan)) {
    $url = '/th13/ja/';
    header("location: " . $url);
} else {
    $url = '/th13/zh/';
    header("location: " . $url);
}