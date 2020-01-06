<?php
/**
 * 用來支援同步的功能
 * 
 * 傳送參數
 * [$_GET]
 * uuid: FingerPint
 * timestamp: Device Timestamp
 * 
 * [$_POST]
 * logs
 */
include_once '../config.php';
include_once '../lib/redbeanphp/rb.config.php';
include_once '../helper/javascript_helper.php';
include_once '../helper/log_helper.php';

if (isset($_POST["m"])) {
    //$sql = "DROP TABLE IF EXISTS debug";
    //R::exec($sql);
    
    $debug = R::dispense("debug");
    $debug->message = $_POST["m"];
    //if (isset($_SERVER["HTTP_REFERER"])) {
    //    $debug->referer = $_SERVER["HTTP_REFERER"];
    //}
    $ip = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR');
    $debug->client_ip = $ip;
    $debug->timestamp = R::isoDateTime();
    R::store($debug);
}