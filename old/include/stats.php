<?php

function usersNum($opt){
    global $config;
    global $mydb;
    
    // all users
    if($opt == 1) {
        $countThem = "SELECT id FROM users";
        $mydb->query($countThem);
        return $mydb->recno();
    }
    // male users
    else if($opt == 2) {
        $countThem = "SELECT id FROM users WHERE gender = 1";
        $mydb->query($countThem);
        return $mydb->recno();
    }
    // female users
    else if($opt == 3) {
        $countThem = "SELECT id FROM users WHERE gender = 2";
        $mydb->query($countThem);
        return $mydb->recno();
    }
    // all links
    else if($opt == 4) {
        $countThem = "SELECT id FROM links";
        $mydb->query($countThem);
        return $mydb->recno();
    }
}

function linksStats() {
    global $config;
    global $mydb;
    
    $count = "SELECT domain, COUNT(id) as 'number' FROM links GROUP BY domain ORDER BY number DESC LIMIT 10";
    $mydb->query($count);
    if($mydb->recno()) {
        while($data = $mydb->row()) {
            ?>
            <li><span class="spanDomain"><a href="http://www.<?php echo $data["domain"] ?>" target="_blank"><?php echo $data["domain"] ?></a></span> <span class="spanDomainCount">(<?php echo $data["number"] ?>)</span></li>
            <?php
        }
    }
}

/*
SELECT domain, COUNT(id) as "number"
FROM links
GROUP BY domain
)

*/

?>