<?php
include "../config.php";
include "db.php";

global $config;
$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
global $mydb;

$select = "SELECT mail FROM users";
$mydb->query($select);
if($mydb->recno() > 0) {
    while($mail = $mydb->row()) {
        $a[] = $mail["mail"];
    }
}

$q = $_GET["q"];
 
//lookup all hints from array if length of q>0
if (strlen($q) > 0) {
    $hint = "<ul>";
    for($i=0; $i<count($a); $i++) {
        if (strtolower($q) == strtolower(substr($a[$i], 0, strlen($q)))) {
            $hint = '<li class="txtHintItem" onClick="fill(\'' . $a[$i] . '\');">' . $a[$i] . '</li>';
        }
    }
    $hint = $hint . "</ul>";
}

 // Set output to "no suggestion" if no hint were found
 // or to the correct values
 if ($hint == "<ul></ul>") {
    $response = "No such mail in our database";
 }
 else {
    $response = $hint;
 }

 //output the response
 echo $response;
 ?>