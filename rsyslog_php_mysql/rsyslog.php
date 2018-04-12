#!/usr/bin/php -q
<?php
//connection mysql
$mysqlInfo = [
    "dbms" => "mysql",
    "dbHost" => "192.168.80.11",
    "dbName" => "iptables_icmplog",
    "dbUser" => "admin",
    "dbPasswd" => "1234"
];

$mysqlDSN = sprintf("%s: host=%s; dbname=%s; charset=utf8", $mysqlInfo["dbms"], $mysqlInfo["dbHost"], $mysqlInfo["dbName"]);

$pdo = new PDO($mysqlDSN, $mysqlInfo["dbUser"], $mysqlInfo["dbPasswd"]);

$query = function ($sql) use ($pdo){
    return $pdo->query($sql);
};   

$fetch = function($sql) use ($query) {
    return $query($sql)->fetch();
};

$fetchAll = function($sql) use ($query){
    return $query($sql)->fetchAll();
};

// filter Log Messages
function _match($data){ 
    $basicInfo = array();
    $match = array();    
    // split ( time & hostname ), ( message )
    $buffer = explode("icmpLog", $data);
    // get time & hostname
    preg_match("/([A-Z]{1}[a-z]{2} [\d]{2} [\d]{2}\:[\d]{2}\:[\d]{2}) ([\w]+)/", $buffer[0], $basicInfo);
    // key map value , e.g. IN=ens33 = $array[IN]="ens33"
    $buffer[1] = preg_replace("/\s/", "&", trim($buffer[1]));
    parse_str($buffer[1], $match);
    return array($basicInfo[1], $basicInfo[2], $match);
}

// read log data
if(1 !== ($data = fgets(STDIN))){
    list($time, $hostname, $match) = _match($data);   
    $fileName = "/var/log/iptables/ICMP_" . $match["SRC"] . ".log";
    $msg = "";
    $format = "%s %s IN_IF=%s OUT_IF=%s SRC_IP=%s DST_IP=%s PROTO=%s TTL=%s\n";
    $msg = sprintf($format, $time, $hostname, $match["IN"], $match["OUT"] ?: "NULL", $match["SRC"], $match["DST"], $match["PROTO"], $match["TTL"]);
    error_log($msg, 3, $fileName);    

    //write into mysql
    $sql = sprintf("insert into iptables_icmplog (%s, %s, %s, %s, %s, %s, %s, %s) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", "time", "hostname", "in_if", "out_if", "src_ip", "dst_ip", "proto", "ttl", $time, $hostname, $match["IN"], $match["OUT"] ?: "NULL", $match["SRC"], $match["DST"], $match["PROTO"], $match["TTL"]);
    $query($sql);    
}
