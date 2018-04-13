#!/usr/bin/php -q
<?php
// define
$domainName = "skills.com";
$rpzWhiteList = "/etc/bind/security/db.rpz.whitelist";
$domainForward = "/etc/bind/skills.com/db.forward";
$oldList = shell_exec("cat " . $rpzWhiteList . " | grep -E '^(\w)' | grep -Ev '^(" . $domainName . ")' | awk '{print $1}'");


// process
$oldListArray = explode("\n", $oldList);
array_pop($oldListArray); // delete last array

$newListArray = explode("\n", shell_exec("cat " . $domainForward . " | grep -E '^(\w)' | awk '{print $1}'"));
array_pop($newListArray); // delete last array
$newListArray = array_map(function($value){ return $value . ".skills.com"; }, $newListArray);


// add
$diffListArrayAdd = array_diff($newListArray, $oldListArray);
$diffAddCount = count($diffListArrayAdd);

if(0 === $diffAddCount){
  // some code 
}else{
    foreach($diffListArrayAdd as $key => $value){
        $writeValue = sprintf("%-20s\t%5s\t%17s", $value, "CNAME", "rpz-passthru.");
        $writeCommand = "echo \"" . $writeValue . "\" >> " .  $rpzWhiteList;
        shell_exec($writeCommand);
  }
}

// delete
$diffListArrayDel = array_diff($oldListArray, $newListArray);
$diffDelCount = count($diffListArrayDel);

if(0 === $diffDelCount){
    // some code
}else{
    foreach($diffListArrayDel as $key => $value){
	    $deleteValue = $value;
	    $deleteCommand = "sed -i '/" . $deleteValue .  "/d' " . $rpzWhiteList;
	    shell_exec($deleteCommand);
    }
}
