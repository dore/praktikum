<?php
function addLink() {
    global $config;
	global $mydb;
	
	if($_POST['send']){
		$name = clean($_POST['name']);
		$url = clean($_POST['url']);
		$domain = getdomain($url);

		$errors = array();
        $errMesssage = "";
        
        if(!$name){
			$errors[] = "Title is not defined!";
            $errMesssage = $errMesssage . "Title is not defined!<br />";
		}						
		if(!$url){
			$errors[] = "URL is not defined!";
            $errMesssage = $errMesssage . "URL is not defined!<br />";
		}
		
        /*if($url) {
    		if(!validUrl($url)){
    			$errors[] = "URL is not valid!";
                $errMesssage = $errMesssage . "URL is not valid!<br />";
    		}
		}*/
        
		if(count($errors) > 0){
			/*foreach($errors AS $error){
				echo $error . "<br />";
			}*/
            echo error($errMesssage);
			echo "<br /><a href='?page=addLink'>Back</a>";
		}else {
			$sql = "INSERT INTO links (url, domain, rating, user_id, name, submitter_id, status, map_id) VALUES ('" . $url . "','" . $domain . "', 0,'" . $_SESSION["portal_id"] . "','" . $name . "', '" . $_SESSION["portal_id"] . "', 1, 0)";
			$mydb->query($sql);
			if($mydb->result())
				redirect("index.php?page=allMaps");
            else echo error("There was an error!");
		}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<table align="center" style="padding: 5px;">
				<tr>
					<td>URL:
					</td>
					<td align="left"><input type="text" name="url" class="field" id="fieldUrl" style="width: 160px" />
					</td>
				</tr>
                <tr>
					<td>Title:
					</td>
					<td align="left"><input type="text" name="name" class="field" id="fieldTitle" style="width: 160px" /> 
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="send" value="Ok" class="button addLinkSubmit" style="width: 90px" />&nbsp;
						<input type="reset" name="cancle" value="Cancle" class="button addLinkSubmit" style="width: 90px" /><br />
						<p><a href="#" id="driver">Get Title</a></p>
					</td>
				</tr>
			</table>
			</form>
		<?php
	}
}

function getdomain($url) { 
    
        preg_match ( 
            "/^(http:\/\/|https:\/\/)?([^\/]+)/i", 
            $url, $matches 
        ); 
    
        $host = $matches[2];  
    
        preg_match ( 
            "/[^\.\/]+\.[^\.\/]+$/",  
            $host, $matches 
        ); 
         
        return strtolower("{$matches[0]}"); 
}  
    
function validUrl($url) {
		$url = @parse_url($url);

		if ( ! $url) {
			return false;
		}

		$url = array_map('trim', $url);
		$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
		$path = (isset($url['path'])) ? $url['path'] : '';

		if ($path == '')
		{
			$path = '/';
		}

		$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';

		if ( isset ( $url['host'] ) AND $url['host'] != gethostbyname ( $url['host'] ) )
		{
			if ( PHP_VERSION >= 5 )
			{
				$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
			}
			else
			{
				$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

				if ( ! $fp )
				{
					return false;
				}
				fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
				$headers = fread ( $fp, 128 );
				fclose ( $fp );
			}
			$headers = ( is_array ( $headers ) ) ? implode ( "\n", $headers ) : $headers;
			return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
		}
		return false;
}

function vsiLinki($user_id, $option, $mapId) {
	global $config;
	global $mydb;
	
    if($mapId == -1)
	   $select = "SELECT * FROM links WHERE user_id = " . $user_id . " AND status = 3 ORDER BY rating ASC";
    else
        $select = "SELECT * FROM links WHERE user_id = " . $user_id . " AND map_id = " . $mapId;
	$mydb->query($select);
	
	$i = 1;
    $count = 1;
	if($mydb->recno() == 0) {
	    if($option == 1) {
		?>
		<tr>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>">You have no links yet!</td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
        </tr>
        <?php
        }
        else if($option == 2) {
            ?>
		<tr>
            <td class="tdLink<?php echo $i ?>">Hello there! :) <br />Either you have no links added or you have to choose the links that you want to have on your home page. <br />Click <a href="?page=allMaps">here</a> to select links which will be displayed on your home screen or <a href="#" id="addLinkButton">here</a> to add a new link!</td>
        </tr>
        <?php
        }
	} 
    else {
        while($vrstica = $mydb->row()) {
            enLink($vrstica["id"], $i, $count, $option, $vrstica["submitter_id"], $vrstica["status"]);
            if($i == 1) $i = 2;
            else $i = 1;
            $count++;
		}
        if($option == 2)
            echo "</tr>";
	}
}

function enLink($id, $i, $count, $option, $senderId, $status) {
	global $config;
	global $mydb1;
    
    $select = "SELECT * FROM users WHERE id = " . $senderId;
	$mydb1->query($select);
	if($mydb1->recno() == 1) {
	   $vrstica = $mydb1->row();
	   $sender = $vrstica["name"] . " " . $vrstica["surname"];
	}
	$select = "SELECT * FROM links WHERE id = " . $id;
	$mydb1->query($select);
	
	if($mydb1->recno() == 1) {
		$vrstica = $mydb1->row();
        if($option == 1) {
		?>
        <tr class="tdLink<?php echo $i ?>" id="<?php echo $count ?>">
            <td><?php echo $count ?></td>
            <td><?php echo skrci($vrstica["name"]); ?></td>
            <td><a href="<?php echo $vrstica["url"]; ?>" title="<?php echo $vrstica["url"]; ?>" target="_blank"><?php echo skrci($vrstica["domain"]); ?></a></td>
            <td><?php echo "<span class='pLinkUrl'>" . skrci($vrstica["url"]) . "</span>"; ?></td>
            <td>
                <?php
                if($status == 2 || $_SESSION["portal_id"] != $senderId) {
                    echo "<span class='pLinkSender'>Sender: <b>" . $sender . "</b></span>";
                } else {
                    echo "<span class='pLinkUrl'>Link is yours</span>";
                }
                ?>
            </td>
            <td><a href="?page=manageLinks&izbrisiLink=<?php echo $vrstica["id"] ?>"><img src="images/delete.png" alt="up" title="Delete" /></a></td>
        </tr>
		<?php
        }
        else if($option == 2) {
            if($count == 1) {
                ?>
                <tr>
                <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" title="<?php echo $vrstica["url"] ?>"  onclick="window.open('<?php echo $vrstica["url"] ?>', '_blank');">
                    <?php
                    if($status == 2 || $_SESSION["portal_id"] != $senderId) {
                        echo "<p class='pLinkSender'>Sent by: <b>" . $sender . "</b></p>";
                    }
                    ?>
                    <p class="pLinkName"><?php echo $vrstica["name"] ?></p>
                    <p class="pLinkDomain"><?php echo $vrstica["domain"] ?></p>
                    <p class="pLinkUrl"><?php echo $vrstica["url"] ?></p>
                </td>               
        		<?php
            }
            else if((($count-1) % 3) == 0) {
                ?>
                </tr>
                <tr>
                <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" title="<?php echo $vrstica["url"] ?>"  onclick="window.open('<?php echo $vrstica["url"] ?>', '_blank');">
                    <?php
                    if($status == 2 || $_SESSION["portal_id"] != $senderId) {
                        echo "<p class='pLinkSender'>Sent by: <b>" . $sender . "</b></p>";
                    }
                    ?>
                    <p class="pLinkName"><?php echo $vrstica["name"] ?></p>
                    <p class="pLinkDomain"><?php echo $vrstica["domain"] ?></p>
                    <p class="pLinkUrl"><?php echo $vrstica["url"] ?></p>
                </td>                    
        		<?php
            }
            else {
                ?>
                <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" title="<?php echo $vrstica["url"] ?>"  onclick="window.open('<?php echo $vrstica["url"] ?>', '_blank');">
                    <?php
                    if($status == 2 || $_SESSION["portal_id"] != $senderId) {
                        echo "<p class='pLinkSender'>Sent by: <b>" . $sender . "</b></p>";
                    }
                    ?>
                    <p class="pLinkName"><?php echo $vrstica["name"] ?></p>
                    <p class="pLinkDomain"><?php echo $vrstica["domain"] ?></p>
                    <p class="pLinkUrl"><?php echo $vrstica["url"] ?></p>
                </td>                    
        		<?php
            }
        }
	}
	else {
		echo "Ne obstaja";
	}
}

function vsiLinkiManage($user_id, $mapId) {
	global $config;
	global $mydb;
	
	$select = "SELECT * FROM links WHERE user_id = " . $user_id . " AND map_id = " . $mapId;
	$mydb->query($select);
	
	$i = 1;
	if($mydb->recno() == 0) {
		?>
		<tr>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>">No unmapped links! [<a href="#" id="addLinkButton">add</a>]</td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
        </tr>
        <?php
	} else {
        while($vrstica = $mydb->row()) {
            enLinkManage($vrstica["id"], $i, $vrstica["user_id"]);
            if($i == 1) $i = 2;
            else $i = 1;
		}
	}
}

function enLinkManage($id, $i, $userId) {
	global $config;
	global $mydb1;
    $mydb2 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
    
    $select = "SELECT * FROM users WHERE id = " . $userId;
	$mydb1->query($select);
	if($mydb1->recno() == 1) {
	   $vrstica = $mydb1->row();
	   $sender = $vrstica["name"] . " " . $vrstica["surname"];
	}
	$select = "SELECT * FROM links WHERE id = " . $id;
	$mydb1->query($select);
	
	if($mydb1->recno()) {
		$vrstica = $mydb1->row();
		?>
        <tr class="tdLink<?php echo $i ?>">
            <td><span title="<?php echo $vrstica["name"]; ?>"><?php echo skrci($vrstica["name"]); ?></span></td>
            <td><a href="<?php echo $vrstica["url"]; ?>" title="<?php echo $vrstica["url"]; ?>" target="_blank"><?php echo skrci($vrstica["domain"]); ?></a></td>
            <td>
                <select onchange="selectMap(this, <?php echo $id; ?>);">
                <?php
                $sql = "SELECT * FROM maps WHERE user_id =" . $userId;
                $mydb2->query($sql);
                if($mydb2->recno() > 0) { 
                    while ($row = $mydb2->row()) {
                        ?>
                        <option value="<?php echo $row["id"]; ?>" <?php if($vrstica["map_id"] == $row["id"]) echo "disabled selected"; ?>><?php echo $row["name"]; ?></option>
                        <?php
                    }
                }
                ?>
                    <option value="0" <?php if($vrstica["map_id"] == 0) echo "disabled selected"; ?>>No map</option>
                </select>
            <td>
                <input type="checkbox" name="homePage" <?php if($vrstica["status"] == 3) echo "checked"; ?> onchange="toHomePage(this, <?php echo $id ?>)" /><br />
            </td>
            </td>
            <td><a href="?page=allMaps&izbrisiLink=<?php echo $vrstica["id"] ?>" onclick="return makesure();"><img src="images/delete.png" alt="up" title="Delete" /></a></td>
        </tr>
		<?php
	} else {
		echo "Ne obstaja";
	}
}

function izbrisiLink($user_id) {
    global $config;
    global $mydb;
    $select = "SELECT user_id FROM links WHERE id =  " . clean($_GET['izbrisiLink']);
    $mydb->query($select);
    if($mydb->recno() == 1)
		$vrstica = $mydb->row();
    if($user_id == $vrstica["user_id"]) {
    	$delete = "DELETE FROM links WHERE id =  " . clean($_GET['izbrisiLink']);
    	$mydb->query($delete);
    	if($mydb->result()) {
            echo info("<br />Selected link was successfully deleted!") . "<a href='?page=manageLinks'>Continue</a>";
            redirect("index.php?page=allMaps");
        } else {
    		echo error("There was an error regarding the database! Please try again later!");
            redirect("index.php?page=allMaps");
        }
    }
    else {
        echo error("You do not have privilages to delete the chosen link!");
    }
}

function sendLink() {
    global $config;
    global $mydb;
    global $mydb1;
    
    if($_POST['send']){
		$name = clean($_POST['name']);
		$url = clean($_POST['url']);
        $email = clean($_POST['reciever']);
		$domain = getdomain($url);

		$errors = array();
        $errMesssage = "";
        
        if(!$name){
			$errors[] = "Title is not defined!";
            $errMesssage = $errMesssage . "Title is not defined!<br />";
		}						
		if(!$url){
			$errors[] = "URL is not defined!";
            $errMesssage = $errMesssage . "URL is not defined!<br />";
		}
        
        if(!$email){
			$errors[] = "Recipient is not defined!";
            $errMesssage = $errMesssage . "Recipient is not defined!<br />";
		}
        
        if($email){
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				if(!preg_match($checkemail, $email)){
					$errors[] = "E-mail is not valid, must be name@server.tld!";
                    $errMesssage = $errMesssage . "E-mail is not valid, must be name@server.tld!<br />";
				}
		}
		
        /*if($url) {
    		if(!validUrl($url)){
    			$errors[] = "URL is not valid!";
                $errMesssage = $errMesssage . "URL is not valid!<br />";
    		}
		}*/
        
		if(count($errors) > 0){
			/*foreach($errors AS $error){
				echo $error . "<br />";
			}*/
            echo error($errMesssage);
			echo "<br /><a href='?page=sendLink'>Back</a>";
		}else {
            $sql = "SELECT id FROM users WHERE mail = '" . $email . "'";
            $mydb->query($sql);
            if($mydb->recno() == 1)
                $recId = $mydb->row();
            else 
                echo error("There was an error!") . "<br /><a href='?page=sendLink'>Back</a>";
                
			$sql = "INSERT INTO links (url, domain, rating, user_id, name, submitter_id, status, map_id) VALUES ('" . $url . "','" . $domain . "', 0,'" . $recId["id"] . "','" . $name . "', '" . $_SESSION["portal_id"] . "', 3, 0)";
			$mydb->query($sql);
			if($mydb->result())
				echo info("You have successfully sent <b>" . $name . "</b> to <b>" . $email . "</b>!") . "<br /><a href='?page=allMaps'>Back</a>";
            else 
                echo error("There was an error2!") . "<br /><a href='?page=sendLink'>Back</a>";
		}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<table align="center" style="padding: 5px;">
				<tr>
					<td>URL:
					</td>
					<td align="left"><input type="text" name="url" class="field" style="width: 160px" />
					</td>
				</tr>
                <tr>
					<td>Title:
					</td>
					<td align="left"><input type="text" name="name" class="field" style="width: 160px" /> 
					</td>
				</tr>
				<tr>
					<td valign="top">To:
					</td>
					<td><input type="text" name="reciever" class="field" id ="inputString" style="width: 160px" onkeyup="showHint(this.value);" />
                    <span id="txtHint" style="cursor: pointer;"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="send" value="Send" class="button sendLinkSubmit" style="width: 90px" />&nbsp;
						<input type="reset" name="cancle" value="Cancle" class="button sendLinkSubmit" style="width: 90px" /><br />
					</td>
				</tr>
			</table>
		<?php
	}
}
?>