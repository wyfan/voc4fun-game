<?php
if (function_exists("add_log") === false) {
    function add_log($opt) {
        if (is_array($opt) === FALSE || isset($opt["uuid"]) === FALSE) {
            return;
        } 
        
        $log = R::dispense("log");
        $log->uuid = $opt["uuid"];
        $log->timestamp = get_javascript_time();
        
        if (isset($opt["file_name"])) {
            $log->file_name = $opt["file_name"];
        }
        if (isset($opt["function_name"])) {
            $log->function_name = $opt["function_name"];
        }
        if (isset($opt["qualifier"])) {
            $log->qualifier = $opt["qualifier"];
        }
        if (isset($opt["data"])) {
            $log->data = json_encode($opt["data"], JSON_UNESCAPED_UNICODE);
        }
        
        R::store($log);
    }
}

//if (function_exists("find_name") === FALSE) {
//    function find_name($uuid) {
//        
//    }
//}

// ----------------------------------------------

// 先建立必要的表格
// CREATE VIEW uuid_name AS SELECT l1.uuid, l1.data AS name FROM log AS l1, (SELECT uuid, max(timestamp) AS timestamp FROM log WHERE file_name = 'controller_profile.js' AND function_name = 'change_user_name()' GROUP BY uuid) AS l2 WHERE l1.uuid = l2.uuid AND l1.timestamp = l2.timestamp

$views = array(
    // VIEW: uuid_name
    "uuid_name" => 'CREATE VIEW uuid_name AS 
SELECT l1.uuid,
    btrim(l1.data, \'"\'::text) AS name
   FROM log l1,
    ( SELECT max(log.id) AS id,
            log.uuid,
            max(log."timestamp") AS "timestamp"
           FROM log
          WHERE log.file_name = \'controller_profile.js\'::text AND log.function_name = \'change_user_name()\'::text
          GROUP BY log.uuid) l2
  WHERE l1.id = l2.id AND l1.uuid = l2.uuid AND l1."timestamp" = l2."timestamp";',
    
     // VIEW: note
    "note" => 
    'CREATE VIEW note AS 
 SELECT l1.uuid,
    uuid_name.name,
    l1.qualifier AS q,
    l1.data::json ->> \'note\'::text AS note,
    l1."timestamp"
   FROM log l1
     JOIN uuid_name USING (uuid),
    ( SELECT max(log.id) AS id,
            log.uuid,
            max(log."timestamp") AS "timestamp",
            log.qualifier
           FROM log
          WHERE log.file_name = \'controller_note.js\'::text AND log.function_name = \'submit()\'::text
          GROUP BY log.uuid, log.qualifier) l2
  WHERE l1.id = l2.id AND l1.uuid = l2.uuid AND l1."timestamp" = l2."timestamp"
  ORDER BY l1."timestamp" DESC;'
);

$exists = R::getRow("
SELECT EXISTS(
    SELECT * 
    FROM information_schema.tables 
    WHERE 
      table_schema = 'public' AND 
      table_name = 'uuid_name'
);");

if (isset($exists["exists"]) === false || $exists["exists"] === false) {
    //echo "1";
    foreach ($views AS $sql) {
        //echo $sql;
        R::exec($sql);
    }
}