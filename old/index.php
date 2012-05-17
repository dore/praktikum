<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
global $config;
global $mydb;
session_start();
header("Chache-control: private");
// Includes
require_once("include/general.php");
require_once("include/register.php");
require_once("include/link.php");
require_once("include/stats.php");
require_once("include/notes.php");
require_once("include/maps.php");
include "config.php";
include "include/db.php";
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
<head>
	<meta http-equiv="Content-Language" content="SI" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="revisit-after" content="15 days" />
    <link rel="stylesheet" href="styles/orig.css" type="text/css" media="all" />
    <!--[if IE]> <link rel="stylesheet" type="text/css" href="styles/orig_ie.css" /> <![endif]--> 
    <link rel="shortcut icon" href="images/icons/favicon48x48.ico" />
    <script type="text/javascript" src="include/jquery-1.5.min.js"></script>
    <script type="text/javascript" src="include/jquery.tablednd_0_5.js"></script>
    <script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="include/scripts.js"></script>
	<title>
    	<?php
		global $config;
		echo $config["siteTitle"];
		?>
    </title>
</head>
<body>
	<div id="divTopRob">
    	<div id="divTopMeni">
            <!-- Main menu -->      
            <?php
            // logged in or not...
			if($_SESSION["portal_status"] == 1) {
				?>
                <div id="divLoginApplied">
					Welcome back <?php echo $_SESSION["portal_user"]; ?>! &nbsp; [ <a href="?odjava" title="Log Off">Log Off</a> ]
                </div>
                <?php
			}
			else {
    			?>
                <div id="divLogin">
                <form name="form1" method="post" action="<?php echo $PHP_SELF; ?>">
                    <input type="text" name="user_mail" value="e-mail" title="e-mail" onfocus="userField_Focus(this);" onblur="userField_Blur(this);" class="fieldLogin" />&nbsp;&nbsp;
                    <input id="inputPass" type="password" name="user_pass" title="Geslo" value="Geslo" onfocus="passwordField_Focus(this);" onblur="passwordField_Blur(this);" onkeyup="checkKey(event)" class="fieldLogin" />&nbsp;&nbsp;
                    <input type="checkbox" name="autologin" value="1" />Remember Me &nbsp;
    	            <input type="submit" name="potrdi" value="Log in" id="buttonLogIn" /> &nbsp;
                </form>
                </div>
                <?php
			}
            //
			?>
            <div id="divUlPageNavHolder">
                <ul id="ulPageNav">
                    <li class="divMeniItem" onclick="document.location.href='index.php';">Home</li>
                    <li class="divMeniItem" onclick="document.location.href='?page=about';">About</li>
                    <?php 
                    if($_SESSION["portal_status"] == 1) echo "<li class=\"divMeniItem\" onclick=\"document.location.href='?page=stats';\">Statistic</li>";
                    else echo "<li class=\"divMeniItem\" onclick=\"document.location.href='?page=register';\">Register</li>";  
                    ?>
                </ul>
            </div>
            <!-- Main content holder -->
            <div id="divContent0">
            <div id="divContentMeni">
            	<h2>Meni</h2>
                <?php 
				if($_SESSION["portal_status"] == 1) {
					?>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="?page=search">Search WWWeb</a></li>
                        <br />
                        <li><a href="#" onclick="menu('links')">Links</a></li>
                            <ul id="links" class="innerMenu" style="display: none;">
                            	<?php menuMaps(); ?>
                            </ul>     
                        <li><a href="#" onclick="menu('manageLinks')">Send</a></li>
                            <ul id="manageLinks" class="innerMenu" style="display: none;">
                            	<li><a href="?page=sendLink">Link</a></li>
                                <li><a href="?page=sendNote">Note</a></li>
                            </ul>
                        <br />
                        <li><a href="?page=manage">Control Panel</a></li>
                        <li><a href="?page=extras">Extras</a></li>
                    </ul>
                    <?php
                    if($_SESSION["portal_priv"] == 2) {
                        ?>
                        <ul>
                            <li><a href="admin">Administration</a></li>
                        </ul>
                        <?php 
                    }
				}
				else {
					?>
					[ you are not logged in ]
					<?php 
				} 
				?>
            </div>
            <div id="divContent1">
					<?php
                    if(isset($_GET["page"])) {
                        switch($_GET['page']) {
                            case "about":
                                ?>
                                    <h1>[ About ]</h1>
                                    <h2>What is myLink</h2>
                                    <p class="pText">
                                        Have you ever lost an important link? <br />
                                        Have you ever gone to work and later found out that you have forgotten a link about an <b>important</b> research, written on a pice of paper on the table of your living room?<br />
                                        Have you ever visited a friend wanting to show him an awesome video, music video, online game, etc. and suddenly, you were like: "%:#+@?*! Where did I find that?!?"<br />
                                        Have you ever lost a list of paper with important notes?
                                    </p>
                                    <p class="pText">
                                        <b>NOT any more!</b> myLink is here just to store your favourite links and notes! <br />
                                        Accessibility anywhere and anytime makes it an ideal web application for everyone, who uses multiple computers - home, school, collage, work, etc. and likes to have his favourites on hand.<br />
                                        It's very easy to use and once you get used to it, there is no going back to saving your links on notes or USB key in text files. <br />
                                        And by the way. Your given information will not be used in any way, except for statistics. It's absolutely free of charge for all users, so don't worry about that.<br /><br />
                                        So what are you waiting for... Christmas? :) <br />
                                        <a href='?page=register'>Register</a> and start using it! :)<br /><br />
                                        
                                        <img src="images/graf1.png" alt="How does it work?" title="How does it work?"/>
                                        <p class="pSubtitles">How does myLink work</p>
                                        
                                        <input class="button" type="submit" value="Register" onclick="document.location.href = 'index.php?page=register';" />
                                        <input class="button" type="submit" value="Add to favourites" onclick="bookmarksite(document.title, 'http://mylink.si');" />
                                        <!--[if IE]><input class="button" type="submit" value="Set as homepage" onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://mylink.si');" /><![endif]-->
                                    </p>
                                    <br />
                                    <h3>Similar design?</h3>
                                    <p class="pText">
                                        Why do I remember on facebook when I see this page?<br />
                                        We figured, that most people use facebook several times a day. In fact, everytime they go on the web they check it. <br />
                                        Let's say that you have myLink set as your homepage and you have facebook added to your links. 
                                        When you open your browser you are one click away from facebook, yet you already have the feeling that you are using it... 
                                        Even better, you are one click away from any page you wish on all computers you use.
                                    </p>
                                    <p class="pText"><img src="images/mail.png" alt="mail" title="e-mail" style="border: 0px;" /></p>
                                <?php
                            break;
                            case "register":
                                ?>
                                    <h1>[ Register ]</h1>
                                <?php
								register();
                            break;
                            case "addNote":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Add Note ]</h1>
                                <?php
								addNote();
                            break;
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
                            case "sendNote":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Send Note ]</h1>
                                <?php
								sendNote();
                            break;
                            case "allNotes":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Your Notes ]</h1>
                                    <table cellpadding="0" cellspacing="0" id="tableLinks">
                                    <tr>
                                        <td class="tdLinkHeadder"></td>
                                        <td class="tdLinkHeadder">Title</td>
                                        <td class="tdLinkHeadder">Text preview</td>
                                        <td class="tdLinkHeadder">From</td>
                                        <td class="tdLinkHeadder">Delete/View</td>
                                    </tr>
                                <?php
								allNotes($_SESSION["portal_id"]);
                                ?>
                                    </table>
                                <?php
                                if(isset($_GET["viewNote"]))
                                    viewNote($_SESSION["portal_id"]);
                                if(isset($_GET["izbrisiNote"]))
                                    izbrisiNote($_SESSION["portal_id"]);
                            break;
                            case "extras":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Extras ]</h1>
                                    <p class="pText">
                                        <table cellpadding="0" cellspacing="0" id="tableRegister">
                                            <tr>
                                                <td><img src="images/wallpapers/Wallpaper1Small.png" alt="Wallpaper 1" title="Preview" /></td>
                                                <td>
                                                    <h3>Wallpaper 1:</h3>
                                                    <ul>
                                                        <li><a href="images/wallpapers/wallpaper1-1024x768.jpg" target="_blank">1024x768</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1280x1024.jpg" target="_blank">1280x1024</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1440x900.jpg" target="_blank">1440x900</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1600x1200.jpg" target="_blank">1600x1200</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1920x1080.jpg" target="_blank">1920x1080</a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h3>Userbar:</h3>
                                                    <img style="border: 0px;" src="images/wallpapers/userbar.png" alt="Userbar" title="Preview" />
                                                    <input type="text" class="field" value="[img]http://mylink.si/images/wallpapers/userbar.png[/img]" style="width: 360px;" />
                                                    <p class="pSubtitles">*Copy/Paste code</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </p>
                                    
                                <?php
                            break;
                            case "search":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Serch the WWWeb ]</h1>
                                    
                                    <p class="pText">Select your favourite searcher or search with one of them.</p>
                                    <?php
                                    selectBrowser($_SESSION["portal_id"]);
                                    ?>
                                    <p class="pText">
                                        <form method="get" action="http://www.google.com/search" target="_blank">
                                            <input class="field" type="text" name="q" />
                                            <input class="button" type="submit" value="Google Search" />
                                        </form>
                                    </p>
                                    <p class="pText">
                                        <form method="get" action="http://www.bing.com/search" target="_blank">
                                            <input class="field" type="text" name="q" />
                                            <input class="button" type="submit" value="Bing Search" />
                                        </form>
                                    </p>
                                    <p class="pText">
                                        <form name="form" method="get" action="http://www.najdi.si/search.jsp" onsubmit="target='_new'; return true;" style="margin: 0pt; padding: 0pt;">
                                            <input name="q" type="text" class="field" />
                                            <input class="button" type="submit" value="Najdi.si Search" />
                                            <input name="st" value="custom" checked="checked" type="hidden" />
                                            <input name="inenc" value="UTF-8" type="hidden" />
                                        </form>
                                    </p>
                                <?php
                            break;
                            case "addLink":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Add Link ]</h1>
                                <?php
								addLink();
                            break;
                            case "sendLink":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Send Link ]</h1>
                                <?php
								sendLink();
                            break;
                            case "addMap":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Add Map ]</h1>
                                    <p class="pText">Here you can add a map to organize your links in categories.</p>
                                <?php
								addMap();
                            break;
                            case "allMaps":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Manage Maps ]</h1>
                                    <input class="button" type="submit" value="Add Link" onclick="document.location.href = '?page=addLink';" />
                                    <input class="button" type="submit" value="Add Map" onclick="document.location.href = '?page=addMap';" />
                                    <p class="pText">Maps are folders by which you can organize your links. Each link can be assigned to one map.</p>
                                    <p class="pText">Manage your maps:</p>
                                    <table cellpadding="0" cellspacing="0" id="tableLinks" style="width: 250px;">
                                    <tr>
                                        <td class="tdLinkHeadder">Map name</td>
                                        <td class="tdLinkHeadder">Delete</td>
                                    </tr>
                                <?php
								allMaps();
                                ?>
                                    </table>
                                    <p class="pSubtitles"><?php echo warning("Deleting a map will also delete its links!"); ?></p>
                                    <table cellpadding="0" cellspacing="0" id="tableLinks">
                                    <tr>
                                        <td class="tdLinkHeadder">Title</td>
                                        <td class="tdLinkHeadder">Domain</td>
                                        <td class="tdLinkHeadder">Map</td>
                                        <td class="tdLinkHeadder">Display on home page</td>
                                        <td class="tdLinkHeadder">Delete</td>
                                    </tr>
                                <?php
                                    if($_GET["map"]) {
                                        $mapId = clean($_GET["map"]);
                                        vsiLinkiManage($_SESSION["portal_id"], $mapId);
                                    } else {
                                        vsiLinkiManage($_SESSION["portal_id"], 0);
                                    }
                                ?>
                                    </table>
                                <?php
                                if(isset($_GET["deleteMap"])) { 
                                    deleteMap($_SESSION["portal_id"]);
                                }
                                if($_GET["action"] == "setMap") {
                                    $link_id = clean($_GET["id"]);
                                    $link_map_id = clean($_GET["value"]);
                                    
                                    $update = "UPDATE links SET map_id = " . $link_map_id . " WHERE id = " . $link_id;
                                    $mydb->query($update);
                                    
                                    redirect("index.php?page=allMaps");
                                }
                                if($_GET["action"] == "setToHomePage") {
                                    $link_id = clean($_GET["id"]);
                                    $link_status = clean($_GET["value"]);
                                    
                                    if($link_status == 1)
                                        $update = "UPDATE links SET status = 3 WHERE id = " . $link_id;
                                    else if($link_status == 0)
                                        $update = "UPDATE links SET status = 1 WHERE id = " . $link_id;
                                    $mydb->query($update);
                                    
                                    redirect("index.php?page=allMaps");
                                }
                            break;
                            case "stats":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Statistics ]</h1>
                                    <p class="pText">
                                    <h2>Users:</h2>
                                    Currently registered users: <b><?php echo usersNum(1); ?></b>
                                        <ul>
                                            <li><b><?php echo usersNum(2); ?></b> men</li>
                                            <li><b><?php echo usersNum(3); ?></b> women</li>
                                        </ul>
                                    </p>
                                    <p class="pText">
                                        <h2>Top 10 domains:</h2>
                                        <ol>
                                            <?php linksStats(); ?>
                                        </ol>
                                        
                                    </p>
                                <?php
                            break;
                            case "manageLinks":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Manage Homepage Links ]</h1>
                                    <script type="text/javascript">
                                   $(document).ready(function() {
                                    	$("#tableLinks").tableDnD();
                                    	$("#tableLinks tr:even').addClass('alt')");
                                    	$("#tableLinks").tableDnD({
                                    	    onDragClass: "myDragClass",
                                    	    onDrop: function(table, row) {
                                                var rows = table.tBodies[0].rows;
                                                var debugStr = "";
                                                
                                                for (var i = 0; i < rows.length; i++) {
                                                    debugStr += rows[i].id + "+";
                                                }
                                                
                                                $.post("index.php?page=manageLinks&action=changeOrder", { order: debugStr }, function(data) {
                                                    //alert(debugStr);
                                                    window.location.reload( false );

                                                });
                                    	    }
                                    	});
                                    });
                                    </script>
                                <table cellpadding="0" cellspacing="0" id="tableLinks">
                                    <?php
    								vsiLinki($_SESSION["portal_id"], 1, -1);
                                    ?>
                                </table>
                                <p class="pSubtitles">*Drag and drop your links to rearrange their order.</p>
                                <?php
                                if($_GET["action"] == "changeOrder") {
                                    global $config;
                                    global $mydb;
                                    global $mydb1;
                                    $data = $_POST["order"];
                                    $newOrder = explode("+", $data);
                                    
                                    $check = "SELECT id FROM links WHERE user_id = " . $_SESSION["portal_id"] . " and status = 3 ORDER BY rating ASC";
                                    $mydb->query($check);
                                    
                                    if($mydb->recno() > 0) {
                                        while($order = $mydb->row()) {
                                            $a[] = $order["id"];
                                        }
                                    }
                                    for($i = 0; $i < sizeof($newOrder); $i++) {
                                        for($j = 0; $j < sizeof($a); $j++) {
                                            if($newOrder[$i] == ($j+1)) {
                                                $update = "UPDATE links SET rating = " . ($i+1) . " WHERE id = " . $a[$j];
                                                $mydb1->query($update);
                                            }
                                        }
                                    }
                                }
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
                            case "manage":
                                ?>
                                <h1>[ Control Panel ]</h1>
                                <br />
                                <input class="button" type="submit" value="Add Link" onclick="document.location.href = '?page=addLink';" />
                                <input class="button" type="submit" value="Add Note" onclick="document.location.href = '?page=addNote';" />
                                <input class="button" type="submit" value="Add Map" onclick="document.location.href = '?page=addMap';" />
                                <br />
                                <table cellpadding="0" cellspacing="0" id="tableLinksHome">
                                    <tr>
                                        <td class="tdLinkHome" onclick="document.location.href = '?page=allMaps';">
                                            <p class="pLinkName">Links</p>
                                            <p class="pLinkDomain">Manage your links</p>
                                            <p class="pLinkUrl">Set your home page, divide links into maps, ...</p>
                                        </td>
                                        <td>&nbsp; &nbsp;</td>
                                        <td class="tdLinkHome" onclick="document.location.href = '?page=allNotes';"> 
                                            <p class="pLinkName">Notes</p>
                                            <p class="pLinkDomain">Manage your links</p>
                                            <p class="pLinkUrl">See your notes, edit, delete, ...</p>
                                        </td>
                                        <td>&nbsp; &nbsp;</td>
                                        <td class="tdLinkHome" onclick="document.location.href = '?page=manageAcc';">
                                            <p class="pLinkName">Account</p>
                                            <p class="pLinkDomain">Manage your account</p>
                                            <p class="pLinkUrl">Change your mail, password, ...</p>
                                        </td>
                                    </tr>
                                        <td colspan="5"><br /></td>
                                    <tr> 
                                        <td class="tdLinkHome" onclick="document.location.href = '?page=manageLinks';">
                                            <p class="pLinkName">Home Page</p>
                                            <p class="pLinkDomain">Manage your Home Page</p>
                                            <p class="pLinkUrl">Change the order of links on your Home Page, delete, ...</p>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                            break;
                        }
                    } 
                    else {
                        if($_SESSION["portal_status"] == 1) {
                            ?>
                            <h1>[ My Links ]</h1>
                            <input class="button" type="submit" value="Add Link" onclick="document.location.href = '?page=addLink';" />
                            <input class="button" type="submit" value="Add Note" onclick="document.location.href = '?page=addNote';" />
                            <?php
                            defaultBrowser($_SESSION["portal_id"]);
                            ?>
                            <p class="pText">Here are your links <?php echo $_SESSION["portal_user"] ?>:</p>
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
                            <p class="pText">Hello there!<br />
                            If you are having troubles remembering your links, you have come to the right place! Try using myLink. It is awesome.
                            </p>
                            <p class="pText">For more info read the <a href="index.php?page=about">about</a> section. :)
                            <input class="button" type="submit" value="Register" onclick="document.location.href = 'index.php?page=register';" />
                            <input class="button" type="submit" value="Add to favourites" onclick="bookmarksite(document.title, 'http://mylink.si');" />
                            <!--[if IE]><input class="button" type="submit" value="Set as homepage" onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://mylink.si');" /><![endif]--><br />
                            <img src="images/graf2.png" alt="graf2" title="Stop!" style="border: 0px;" />
                            <p class="pSubtitles">Don't use notes and USB keys for saving links!</p>
							<img src="images/sample.png" alt="Sample" title="Sample of a registered user" />
                            <p class="pSubtitles">Sample of an user interface</p>
                            </p>
                            <?php
                        }
                    } 
                    if(isset($_GET["izbrisiLink"])) { 
                        izbrisiLink($_SESSION["portal_id"]);
                    }
					?>
    			</div>
                <div id="divCopyright"><!--[if IE]>[ <a href="" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://mylink.si');">Set myLink as my home page!</a> ] <![endif]-->
 &nbsp;[&nbsp;&copy; myLink 2011 &nbsp;] &nbsp;[ <a href="" onclick="bookmarksite(document.title, 'http://mylink.si');">Add myLink to favourites!</a> ] &nbsp; [<a href="?page=addLink">Add Link</a> | <a href="?page=addNote">Add Note</a>]</div>
    		</div>
        </div>
        <!-- Small logo top right -->
        <!--<div id="divLogoTop"><img src="images/logoMali.png" title="Logo" height="30" width="80" alt="LogoBeli" />[ myLink ]</div>  -->     
    </div> 
</body>
</html>