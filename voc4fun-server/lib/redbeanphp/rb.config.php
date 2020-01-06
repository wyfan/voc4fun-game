<?php

include_once 'rb.php';

// 連接方式說明
// http://www.redbeanphp.com/index.php?p=/connection
R::setup('pgsql:host=localhost;dbname=' . $CONFIG["pgsql_db"]["db_name"], $CONFIG["pgsql_db"]["password"], $CONFIG["pgsql_db"]["password"]);
R::setAutoResolve(TRUE);        //Recommended as of version 4.2