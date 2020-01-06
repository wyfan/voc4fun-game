<?php

if (function_exists("json_header") === false) {
    function json_header() {
        header('Content-Type: application/json; charset=utf-8');
    }
}

if (function_exists("jsonp_callback") === false) {
    function jsonp_callback($data) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');  
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        
        if (isset($_GET["callback"])) {
            echo $_GET["callback"] . "(" . $json . ");";
        }
        else {
            echo $json;
        }
    }
}

if (function_exists("get_javascript_time") === false) {
    function get_javascript_time() {
        return time() * 1000;
    }
}
