#!/usr/bin/php -q
<?php
// define
$domainName = "skills.com";
$rpzWhiteList = "/etc/bind/security/db.rpz.whitelist";
$domainForward = "/etc/bind/skills.com/db.forward";
$oldList = shell_exec("cat " . $rpzWhiteList . " | grep -E '^(\w)' | grep -Ev '^(" . $domainName . ")' | awk '{print $1}'");
$newList = shell_exec("cat " . $domainForward . " | grep -E '^(\w)' | awk '{print $1}'");

// process
$oldListArray = explode("\n", trim($oldList));

$newListArray = explode("\n", trim($newList));
$newListArray = array_map(function($value){ return $value . ".skills.com"; }, $newListArray);

function dynamicEdit($newArray, $oldArray){
	global $rpzWhiteList;

	$addArray = array_diff($newArray, $oldArray);
	$delArray = array_diff($oldArray, $newArray);
	$addValue = count($addArray);
	$delValue = count($delArray);
	
	if(0 === $addValue && 0 === $delValue){
		return;
	}

    // add	
	if($addValue > 0){
		foreach($addArray as $key => $value){
			$writeValue = sprintf("%-20s\t%5s\t%17s", $value, "CNAME", "rpz-passthru.");
            $writeCommand = "echo \""  . $writeValue . "\" >> " . $rpzWhiteList;
			shell_exec($writeCommand);
		}
	}

    // delete
	if($delValue > 0){	
		foreach($delArray as $key => $value){
			$removeValue = $value;
			$removeCommand = "sed -i '/" . $removeValue . "/d' "  . $rpzWhiteList; 
			shell_exec($removeCommand);
		}
	}
	return;
}

dynamicEdit($newListArray, $oldListArray);
