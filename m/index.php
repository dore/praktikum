<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN""http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<?php
global $config;
global $mydb;
session_start();
header("Chache-control: private");
// Includes
require_once("../include/general.php");
require_once("../include/link.php");
require_once("../include/maps.php");
include "../config.php";
include "../include/db.php";
//

$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
$mydb1 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);

if(!isset($_SESSION["portal_status"])) {
    $_SESSION["portal_status"] = 0;
    // login with cookies
    if(isset($cookie_name)) {
    	// Check if the cookie exists
    	if(isset($_COOKIE[$cookie_name])) {
    		parse_str($_COOKIE[$cookie_name]);
    	
    		// Make a verification
    		$select = "SELECT * FROM users WHERE mail = '" . $mail . "' AND password = '" . $hash . "'";  
    		$mydb->query($select);
    		if($mydb->recno() == 1) {
    			// Register the session
                $vrstica = $mydb->row();
                if($vrstica["active"] != 1) {
                    echo info("User is not activated. Check your mail for conformation mail.");
                } else {
                    $logini = $vrstica["logins"] + 1;
        			$_SESSION["portal_status"] = 1;
        			$_SESSION["portal_mail"] = $mail;
                    $_SESSION["portal_user"] = $usr;
                    $_SESSION["portal_id"] = $id;
        			if(md5("1") == $role)
        				$_SESSION["portal_priv"] = 1;
        			if(md5("2") == $role)
        				$_SESSION["portal_priv"] = 2;
                    $date = date("Y") . "-" . date("m") . "-" . date("d");
                    $dodajLogin = "UPDATE users SET logins = " . $logini . ", last_login = '" . $date . "' WHERE id = " . $_SESSION["portal_id"];
                    $mydb->query($dodajLogin);
                }
    		}
    	}
    }
    //
}

// log off, remove cookie
if(isset($_GET["odjava"])) {
	$_SESSION["portal_status"] = 0;
	if(isSet($_COOKIE[$cookie_name])) {
		// remove 'site_auth' cookie
		setcookie ($cookie_name, '', time() - $cookie_time);
	}
}

// Login via form, set cookie if checked
if(isset($_POST["user_mail"]) && isset($_POST["user_pass"]) && !isset($_POST["gender"])) {
	$mail = clean($_POST["user_mail"]);
	$pass = clean($_POST["user_pass"]);
	$post_autologin = $_POST['autologin'];
	$select = "SELECT * FROM users WHERE mail = '" . $mail . "' AND password = '" . md5($pass) . "'";  
	$mydb->query($select);
	if($mydb->recno() == 1) {
		$vrstica = $mydb->row();
        if($vrstica["active"] != 1) {
            echo info("User is not activated. Check your mail for conformation mail.");
        } else {
    		$_SESSION["portal_status"] = 1;
    		$_SESSION["portal_user"] = $vrstica["name"];
    		$_SESSION["portal_priv"] = $vrstica["role"];
            $_SESSION["portal_id"] = $vrstica["id"];
            $logini = $vrstica["logins"] + 1;
    		
            // Set cookie
    		if($post_autologin == 1) {
    			$password_hash = md5($pass);
    			setcookie ($cookie_name, 'mail=' . $mail . '&hash=' . $password_hash . '&role=' . md5($vrstica["role"]) . '&usr=' . $vrstica["name"] . '&id=' . $vrstica["id"], time() + $cookie_time);
    		}
            //
            
            $date = date("Y") . "-" . date("m") . "-" . date("d");
            $dodajLogin = "UPDATE users SET logins = " . $logini . ", last_login = '" . $date . "' WHERE id = " . $_SESSION["portal_id"];
            $mydb->query($dodajLogin);
            redirect("index.php");
        }
	}
	else {
		alert ("Vpisali ste napačno geslo!");
		history("-1");
	}
}
//
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
    <link rel="stylesheet" href="Style/default.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="../images/icons/favicoBig.ico" />
    <script type="text/javascript" src="../Scripts/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="../include/jquery.tablednd_0_5.js"></script>
    <script type="text/javascript" src="../Scripts/general.js"></script>
    	<title>
    	<?php
		global $config;
		echo $config["siteTitle"];
		?>
    </title>
</head>
<body>
    <table id="tableMain" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td id="tdNavigacija">
                    <?php if($_SESSION["portal_status"] == 1) { ?>
                        <div style="float: right; display: inline;">
                            <a href="?odjava">Log Off</a>
                        </div>
                        <ul>
                            <li class="divMeniItem" onclick="document.location.href='index.php';">Home</li>
                            <li class="divMeniItem" id="dropDown">Maps</li>
                                <ul class="innerMenu">
                                	<?php menuMaps(); ?>   
                                </ul> 
                        </ul>
                    <?php } else { ?>
                        <ul id="ulPageNav">
                            <li class="divMeniItem" onclick="document.location.href='index.php';">Home</li>                        
                        </ul>  
                   <?php } ?> 
                    
            </td>
        </tr>
        <tr>
            <td id="tdContent" align="center">
            <?php 
                    if(isset($_GET["page"])) {
                        switch($_GET['page']) {
                            case "viewMap":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ View Map ]</h1>
                                    <p class="pText">
                                    <?php
                                    
                                    $select = "SELECT * FROM maps WHERE user_id = " . $_SESSION["portal_id"];
                                	$mydb->query($select);
                  
                                	if($mydb->recno() == 0) {
                                		?>
                                		<li>No maps</li>
                                        <?php
                                	} else {
                                        while($vrstica = $mydb->row()) {
                                            if($vrstica["id"] == $_GET["mapId"]) {
                                                echo $vrstica["name"] . "&nbsp;&nbsp;";
                                            } else {
                                                ?>
                                                <a href="?page=viewMap&mapId=<?php echo $vrstica["id"]; ?>"><?php echo $vrstica["name"]; ?></a>&nbsp;&nbsp;
                                                <?php
                                            }
                                		}
                                	}
                                    ?>
                                    </p>
                                    
                                <?php
                                if($_GET["mapId"]) {
                                    $map_id = clean($_GET["mapId"]);
                                    ?>
                                    <table cellpadding="0" cellspacing="0" id="tableLinksHome">
                                    <?php
    								vsiLinki($_SESSION["portal_id"], 2, $map_id);
                                    ?>
                                    </table>
                                    <?php
                                }
                                
                            break;
                        }
                    } 
                    else {
                        if($_SESSION["portal_status"] == 1) {
                            ?>
                            <h2>[ My Links ]</h2>
                            <?php
                            defaultBrowser($_SESSION["portal_id"]);
                            ?>
                            <table cellpadding="0" cellspacing="0" id="tableLinksHome">
                                    <?php
    								vsiLinki($_SESSION["portal_id"], 2, -1);
                                    ?>
                            </table>
                            <?php
                            
                        }
                        else {
						?>
                             <h1>[ Welcome ]</h1>
                            <form name="form1" method="post" action="<?php echo $PHP_SELF; ?>">
                            <fieldset id="tableRegister">
                                <legend>Login</legend>
                                <table>
                                    <tr>
                                        <td>
                                            <input type="text" name="user_mail" value="e-mail" title="e-mail" onfocus="userField_Focus(this);" onblur="userField_Blur(this);" class="field" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input id="inputPass" type="password" name="user_pass" title="Geslo" value="Geslo" onfocus="passwordField_Focus(this);" onblur="passwordField_Blur(this);" onkeyup="checkKey(event)" class="field" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="autologin" value="1" />Remember Me
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                            	            <input type="submit" name="potrdi" value="Log in" class="button" /><br /><br />
                                        </td>
                                    </tr>
                                    </table>
                            </fieldset>
                            </form>
                            <br /><br />
                            <?php
                        }
                    } 
					?>
            </td>
        </tr>
        <tr>
            <td id="tdCopy">[&nbsp;&copy; myLink 2011 &nbsp;]</td>
        </tr>
    </table>
</body>
</html>