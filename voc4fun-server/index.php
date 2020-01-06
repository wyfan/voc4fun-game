<?php
/**
 * 連線方式 http://localhost/voc4fun/voc4fun-server/
 */

include_once 'config.php';
include_once 'lib/redbeanphp/rb.config.php';
include_once 'helper/javascript_helper.php';
include_once 'helper/log_helper.php';

//$log = R::dispense('log');
//$log->timestamp = time() * 1000;
//$log->file_name = "index.php";
//$log->function_name = null;
//$log->qualifier = null;
//$log->data = json_encode(array("key" => "value2"));
//
//R::store($log);

//$logs = R::dispense('log');
//$logs->import(array(
//    array(
//        'timestamp' => time() * 1000,
//        'file_name' => "index.php",
//        'function_name' => "1"
//    ),
//    array(
//        'timestamp' => time() * 1000,
//        'file_name' => "index.php",
//        'function_name' => "2"
//    )
//));
//$logs->import(array(
//        'timestamp' => time() * 1000,
//        'file_name' => "index.php",
//        'function_name' => "1"
//));
//R::store($logs);
//R::dispense('log');

//echo time() * 1000;

//R::exec("CREATE VIEW uuid_name AS SELECT l1.uuid, l1.data AS name FROM log AS l1, (SELECT uuid, max(timestamp) AS timestamp FROM log WHERE file_name = 'controller_profile.js' AND function_name = 'change_user_name()' GROUP BY uuid) AS l2 WHERE l1.uuid = l2.uuid AND l1.timestamp = l2.timestamp");
//$exists = R::getRow("
//SELECT EXISTS(
//    SELECT * 
//    FROM information_schema.tables 
//    WHERE 
//      table_schema = 'public' AND 
//      table_name = 'log'
//);");
//
//print_r($exists);