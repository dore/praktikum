<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once("include/general.php"); 
   
global $config;
global $mydb;
session_start();
header("Chache-control: private");
// Includes
require_once("include/register.php");
include "config.php";
include "include/db.php";
//**************

$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
$mydb1 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);

//**************

if(!isset($_SESSION["portal_status"])) {
    $_SESSION["portal_status"] = 0;
    // login with cookies
    if(isset($cookie_name)) {
    	// Check if the cookie exists
    	if(isset($_COOKIE[$cookie_name])) {
    		parse_str($_COOKIE[$cookie_name]);
    	
    		// Make a verification
    		$select = "SELECT * FROM users WHERE mail = '" . clean($mail) . "' AND password = '" . clean($hash) . "'";  
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
                    $dodajLogin = "UPDATE users SET logins = " . clean($logini) . ", last_login = '" . clean($date) . "' WHERE id = " . clean($_SESSION["portal_id"]);
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
		setcookie($cookie_name, '', time() - $cookie_time);
	}
}

// Login via form, set cookie if checked
if(isset($_POST["user_mail"]) && isset($_POST["user_pass"]) && !isset($_POST["gender"])) {
	$mail = clean($_POST["user_mail"]);
	$pass = clean($_POST["user_pass"]);
	$post_autologin = $_POST['autologin'];
	$select = "SELECT * FROM users WHERE mail = '" . clean($mail) . "' AND password = '" . md5(clean($pass)) . "'";  
	$mydb->query($select);
	if($mydb->recno() == 1) {
		$vrstica = $mydb->row();
        if($vrstica["active"] != 1) {
            echo info("User is not activated. Check your mail for conformation mail.");
        } else {
    		$_SESSION["portal_status"] = 1;
    		$_SESSION["portal_user"] = $vrstica["name"] . " " . $vrstica["surname"];
    		$_SESSION["portal_priv"] = $vrstica["role"];
            $_SESSION["portal_id"] = $vrstica["id"];
            $logini = $vrstica["logins"] + 1;
    		
            // Set cookie
    		if($post_autologin == 1) {
    			$password_hash = md5($pass);
    			setcookie ($cookie_name, 'mail=' . $mail . '&hash=' . $password_hash . '&role=' . md5($vrstica["role"]) . '&usr=' . $vrstica["name"] . ' ' . $vrstica["surname"] . '&id=' . $vrstica["id"], time() + $cookie_time);
    		}
            //
            
            $date = date("Y") . "-" . date("m") . "-" . date("d");
            $dodajLogin = "UPDATE users SET logins = " . clean($logini) . ", last_login = '" . clean($date) . "' WHERE id = " . clean($_SESSION["portal_id"]);
            $mydb->query($dodajLogin);
            redirect("/");
        }
	}
	else {
		alert ("Vpisali ste napačno geslo!");
		history("-1");
	}
}
//
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <title>
        <?php
        global $config;
        echo $config["siteTitle"];
        ?>
    </title>
</head>

<body> 
    <table id="tableTitle" cellpadding="0" cellspacing="0">
        <tr>
            <td id="tdLevoTableTitle">
				<h1>MyLink.si</h1>
				<p>Favourites - whenever, wherever</p>
			</td>
			<td id="tdDesnoTableTitle"><?php if($_SESSION["portal_status"] == 1) { ?><a href="index.php?page=manageAcc" title="Edit account"><img src="images/icons/user.png" alt="User Pic" /></a>&nbsp;&nbsp;<?php echo $_SESSION["portal_user"]; } ?></td>
        </tr>
    </table>
    <table id="tableMain" align="left" cellpadding="0" cellspacing="0">
        <tr>
            <td id="tdContent" align="center">
            <?php 
                    if(isset($_GET["page"])) {
                        switch($_GET['page']) {
                            case "register":
                                ?>
                                    <h1>[ Register ]</h1>
                                <?php
								register();
                            break;
                            case "manageAcc":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Manage your Account ]</h1>
                                <?php
        						editUser($_SESSION["portal_id"]);
                            break;
                        }
                    } 
                    else {
                        if($_SESSION["portal_status"] == 1) {
                            ?>
                            <h1>[ My Links ]</h1>
                            <table cellpadding="0" cellspacing="4" id="tableLinksHome">
                                    <a href="index.php?page=manageAcc">Uredi račun</a>
                            </table>
                            <?php
                            
                        }
                        else {
						?>
                             <h1>[ Welcome ]</h1>
                            <form name="form1" method="post" action="<?php echo $PHP_SELF; ?>">
                                <table align="center">
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
                                            <a href="index.php?page=register">Register</a> 
                                        </td>
                                    </tr>
								</table>
                            </form>
                            <br /><br />
                            <?php
                        }
                    } 
					?>
            </td>
        </tr>
    </table>
</body>
</html>