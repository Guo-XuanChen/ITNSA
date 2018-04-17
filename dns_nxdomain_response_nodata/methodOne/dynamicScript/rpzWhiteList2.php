#!/usr/bin/php -q
<?php
/*
    File    : rpzWhiteList2.php
    Time    : 2018/04/17 18:51:41
    Author  : Guo,Xuan-Chen
*/

/* anaylze file to array (old & new) */
$domainName = "skills.com";
$rpzFile = "/etc/bind/security/db.rpz.whitelist";
$domainFile = "/etc/bind/skills.com/db.forward";
$oldFileValue = shell_exec("cat " . $rpzFile . " | grep -E '^(\w)' |  awk '{print $1}' | sed '1d'");
$newFileValue = shell_exec("cat " . $domainFile . " | grep -E '^(\w)' | awk '{print $1}'");
$oldArray = explode("\n", trim($oldFileValue));
$newArray = explode("\n", trim($newFileValue));
$newArray = array_map(function($value){ return $value . ".skills.com"; }, $newArray);

/* process */
function dynamicEdit($newArray, $oldArray){
	global $rpzFile;

    /* difference array */
	$addArray = array_diff($newArray, $oldArray);
	$delArray = array_diff($oldArray, $newArray);
	$addValue = count($addArray);
	$delValue = count($delArray);

    /* not change */
	if(0 === $addValue && 0 === $delValue){
		return;
	}

    /* add RR */
	if($addValue > 0){
		foreach($addArray as $key => $value){
			$writeValue = sprintf("%-20s\t%5s\t%17s", $value, "CNAME", "rpz-passthru.");
            shell_exec("echo \""  . $writeValue . "\" >> " . $rpzFile);
		}
	}

    /* delete RR */
	if($delValue > 0){	
		foreach($delArray as $key => $value){
			shell_exec("sed -i '/" . $value . "/d' "  . $rpzFile); 
		}
	}
	return;
}

dynamicEdit($newArray, $oldArray);
