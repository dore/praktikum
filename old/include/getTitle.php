<?php
if(isset($_REQUEST["getLink"])) {
    if($_GET["getLink"] != "") {
        $url = $_GET["getLink"];
        $fh = fopen($url, "r");
        $str = fread($fh, 15000);
        fclose($fh);
        $str2 = strtolower($str);
        $start = strpos($str2, "<title>")+7;
        $len = strpos($str2, "</title>") - $start;
        echo substr($str, $start, $len);
    } else {
        echo "Enter the URL first!";
    }
}
?>