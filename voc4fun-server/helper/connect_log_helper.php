<?php

if (function_exists("add_query_log") === false) {

    function add_query_log($result) {
        $table = R::dispense('query_log');
        $table->result = $result;

        $ip = getenv('HTTP_CLIENT_IP')? :
                getenv('HTTP_X_FORWARDED_FOR')? :
                        getenv('HTTP_X_FORWARDED')? :
                                getenv('HTTP_FORWARDED_FOR')? :
                                        getenv('HTTP_FORWARDED')? :
                                                getenv('REMOTE_ADDR');
        $table->ip = $ip;
        $table->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $table->http_referer = $_SERVER["HTTP_REFERER"];

        $table->get = json_encode($_GET, JSON_UNESCAPED_UNICODE);
        $table->post = json_encode($_POST, JSON_UNESCAPED_UNICODE);

        R::store($table);
    }

}