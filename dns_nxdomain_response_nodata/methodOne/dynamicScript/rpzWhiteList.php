#!/usr/bin/php -q
<?php
/*
    File    : rpzWhiteList.php
    TIme    : 2018/04/17 18:13:25
    Author  : Guo,Xuan-Chen
*/

/* anaylze file to array (old, new) */
$domainName = "skills.com";
$rpzFile = "/etc/bind/security/db.rpz.whitelist";
$domainFile = "/etc/bind/skills.com/db.forward";
$oldFileValue = shell_exec("cat " . $rpzFile . " | grep -E '^(\w)' | awk '{print $1}' | sed '1d'");
$newFileValue = shell_exec("cat " . $domainFile . " | grep -E '^(\w)' | awk '{print $1}'");
$oldArray = explode("\n", trim($oldFileValue));
$newArray = explode("\n", trim($newFileValue));
$newArray = array_map(function($value){ return $value . ".skills.com"; }, $newArray);

/* difference array (old & new) */
$addArray = array_diff($newArray, $oldArray);
$delArray = array_diff($oldArray, $newArray);
$addCount = count($addArray);
$delCount = count($delArray);

/* add RR */
if(0 !== $addCount){
    foreach($addArray as $key => $value){
        $writeValue = sprintf("%-20s\t%5s\t%17s", $value, "CNAME", "rpz-passthru.");
        shell_exec("echo \"" . $writeValue . "\" >> " . $rpzFile);       
    }
}

/* del RR  */
if(0 !== $delCount){
    foreach($delArray as $key => $value){
        shell_exec("sed -i '/" . $value . "/d' " . $rpzFile);
    }
}
