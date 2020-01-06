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

$sync_file_name = "db_log.js";
$sync_function_name = "sync_complete()";

if (isset($_GET) && count($_GET) > 0) {
    // 查詢模式
    
    // http://localhost/voc4fun/voc4fun-server/model/sync.php?uuid=1&timestamp=1448797722000
    
    if (isset($_GET["uuid"]) && isset($_GET["timestamp"])) {
        $uuid = $_GET["uuid"];
        $device_timestamp = floatval($_GET["timestamp"]);
    }
    else {
        exit();
    }
    
    $log = R::getRow( 'SELECT file_name, function_name, timestamp FROM log '
            . 'WHERE uuid = ? AND timestamp > ' . $device_timestamp
            . ' LIMIT 1 ', [$uuid] );
    
    if (count($log) === 0) {
        // push 模式 part 1: 送出server上最新的timestamp，等待手機回傳資料
        $log = R::getRow( 'SELECT timestamp FROM log WHERE uuid = ? AND timestamp IS NOT NULL '
                    . 'ORDER BY timestamp DESC LIMIT 1', [$uuid] );
        
        if (count($log) > 0) {
            $timestamp = $log["timestamp"];
            $timestamp = floatval($timestamp);
            //print_r($log);
            if ($device_timestamp === $timestamp) {
                $timestamp = true;
            }
            jsonp_callback($timestamp);
        }
        else {
            jsonp_callback(0);
        }
        exit();
    }
    else {
        // pull 模式: 直接回傳所有資料
        if (count($log) > 1 && isset($log[0])) {
            $log = $log[0];
        }
        
        //$last_sync_timestamp = $log["timestamp"];
        $last_sync_timestamp = $device_timestamp;
        
        $logs = R::getAll( 'SELECT timestamp, file_name, function_name, data FROM log '
                . 'WHERE uuid = ? AND timestamp > ? '
                . 'ORDER BY timestamp ASC', 
                [$uuid, $last_sync_timestamp] );
        
//        print_r($logs);
//        // 把裡面的data json_decode
        foreach ($logs AS $i => $l) {
            //echo floatval($l["timestamp"]);
            $logs[$i]["timestamp"] = floatval($l["timestamp"]);
        }
        //for ($i = 0; $i < count($logs); $i++) {
        //    $logs[$i]["timestamp"] = floatval($logs[$i]["timestamp"]);
        //}
        
        
        if (!($log["file_name"] === $sync_file_name 
                && $log["function_name"] === $sync_function_name)) {
            // match 模式：回傳需要比對的資料 (最新的資料不等於同步資料)
            // @TODO match 模式 尚未完成
        }
        
        if (is_null($logs)) {
            $logs = array();
        }
        
        jsonp_callback($logs);
        
        // 完成同步之後，要留下記錄
        //sync_complete($uuid, "pull");
        
        exit();
    }
}
else if (isset($_POST) && count($_POST) > 0) {
    // push 模式 part 2：從手機上傳送資料給伺服器
    if (isset($_POST["logs"]) && isset($_POST["uuid"])) {
        $uuid = $_POST["uuid"];
        $logs_ary = json_decode($_POST["logs"]);
    }
    else {
        exit();
    }
    
    //print_r($logs_ary);
    
    if (count($logs_ary) > 1) { 
        $log_beans = R::dispense("log", count($logs_ary));
        foreach ($logs_ary AS $i => $log) {
            $log_beans[$i]->uuid = $uuid;
            $log_beans[$i]->import($log);
        }
        R::storeAll($log_beans);
    }
    else if (count($logs_ary) === 1) {  
        $log_bean = R::dispense("log");
        $log_bean->uuid = $uuid;
        $log_bean->import($logs_ary);
        R::store($log_bean);
    }
    
    //sync_complete($uuid, "push");
    
    jsonp_callback(true);
}