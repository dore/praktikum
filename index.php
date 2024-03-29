﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once("include/general.php"); 
$mobile_browser = '0';
 
if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $mobile_browser++;
}
 
if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
    $mobile_browser++;
}    
 
$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
$mobile_agents = array(
    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
    'wapr','webc','winw','winw','xda ','xda-');
 
if (in_array($mobile_ua,$mobile_agents)) {
    $mobile_browser++;
}
 
if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
    $mobile_browser++;
}
 
if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
    $mobile_browser = 0;
}
 
if ($mobile_browser > 0) {
   redirect("m/index.php");
}
else { 
    
global $config;
global $mydb;
session_start();
header("Chache-control: private");
// Includes
require_once("include/register.php");
require_once("include/link.php");
require_once("include/stats.php");
require_once("include/maps.php");
include "config.php";
include "include/db.php";
//**************

$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
$mydb1 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);

//mylink.si GO - url shortener!!!
if(isset($_GET["go"])) {
	redirect($_GET["go"]);
}

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
                    $_SESSION["portal_user"] =  $vrstica["name"] . " " . $vrstica["surname"];
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
    <meta property="og:title" content="MyLink.si"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="http://mylink.si"/>
    <meta property="fb:app_id" content="264106710283984"/>
    <meta property="og:image" content="http://www.mylink.si/images/wallpapers/wallpaper1-1024x768.jpg"/>
    <meta property="og:description" content="MyLink is a web application that saves your favourites! Accessibility anywhere and anytime makes it an ideal web application for everyone, who uses multiple computers - home, school, collage, work, etc. and likes to have his favourites on hand." />
    <link rel="stylesheet" href="/Style/default.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="/images/icons/favicoBig.ico" />
    <!--<script type="text/javascript" src="/Scripts/jquery-1.6.1.min.js"></script>-->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="/include/jquery.tablednd_0_5.js"></script>
    <script type="text/javascript" src="/Scripts/general.js"></script>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript" src="/include/js-class.js"></script>
	<script type="text/javascript" src="/include/bluff-src.js"></script>
	<script type="text/javascript" src="/include/excanvas.js"></script>
    <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
    <script>
    FB.init({ 
        appId:'264106710283984', cookie:true, 
        status:true, xfbml:true 
    });
    </script>
    <title>
        <?php
        global $config;
        echo $config["siteTitle"];
        ?>
    </title>
</head>

<body>
    <div id="fb-root"></div>
		<div id="charmsRightActivate">Swipe in</div>
		<?php if($_SESSION["portal_status"] == 1) { ?>
		<div id="addLink">
			<h1>Add Link</h1>
			<?php addLink(); ?>
		</div>
		<div id="addMap">
			<h1>Add Folder</h1>
			<?php addMap(); ?>
		</div>
		<div id="sendLink">
			<h1>Send Link</h1>
			<?php sendLink(); ?>
		</div>
		<div id="charmsRight">
		<table cellpadding="0" cellspacing="0" align="center" style="height: 100%;">
			<tr>
				<td valign="middle">
					<ul id="ulPageNav">
						<li class="divMeniItem" onclick="document.location.href='/';"><img src="images/icons/home.png" alt="Home" /><br />Home</li>
						<li class="divMeniItem" id="dropDown"><img src="images/icons/folder.png" alt="Folders" /><br />Folders</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?page=search';"><img src="images/icons/search.png" alt="Search" /><br />WWWeb</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?page=manage';"><img src="images/icons/settings.png" alt="Settings" /><br />Ctrl Panel</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?page=extras';"><img src="images/icons/extra.png" alt="Extras" /><br />Extras</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?page=stats';"><img src="images/icons/stats.png" alt="Statistic" /><br />Statistic</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?page=about';"><img src="images/icons/about.png" alt="About" /><br />About</li>
						<li class="divMeniItem" onclick="document.location.href='index.php?odjava';"><img src="images/icons/logout.png" alt="Log Out" /><br />Log Out</li>
					</ul>
				</td>
			</tr>
		</table>
		</div> 	
		<?php }
		else { ?>
		<div id="charmsRight">
			<ul id="ulPageNav">
				<li class="divMeniItem" onclick="document.location.href='/';"><img src="images/icons/home.png" alt="Home" /><br />Home</li>
				<li class="divMeniItem" onclick="document.location.href='index.php?page=about';"><img src="images/icons/about.png" alt="About" /><br />About</li>
				<li class="divMeniItem" onclick="document.location.href='index.php?page=register';"><img src="images/icons/register.png" alt="Register" /><br />Register</li>                        
			</ul> 
		</div> 			
	   <?php } ?>   
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
                            case "about":
                                ?>
                                    <h1>[ About ]</h1>
                                    <h2>What is myLink</h2>
                                    <p class="pText">
                                        Have you ever lost an important link? <br />
                                        Have you ever gone to work and later found out that you have forgotten a link about an <b>important</b> research, written on a pice of paper on the table of your living room?<br />
                                        Have you ever visited a friend wanting to show him an awesome video, music video, online game, etc. and suddenly, you were like: "%:#+@?*! Where did I find that?!?"<br />
                                    </p>
                                    <p class="pText">
                                        <b>NOT any more!</b> myLink.si is here just to store your favourite links! <br />
                                        Accessibility anywhere and anytime makes it an ideal web application for everyone, who uses multiple computers - home, school, collage, work, etc. and likes to have his favourites on hand.<br />
                                        It's very easy to use and once you get used to it, there is no going back to saving your links on notes or USB key in text files. <br />
                                        And by the way. Your given information will not be used in any way, except for statistics. It's absolutely free of charge for all users, so don't worry about that.<br /><br />
                                        So what are you waiting for... Christmas? :) <br />
                                        <a href='?page=register'>Register</a> and start using it! :)<br /><br />
                                        </p>
                                        
                                        <input class="button" type="submit" value="Register" onclick="document.location.href = 'index.php?page=register';" />
                                        <input class="button" type="submit" value="Add to favourites" onclick="bookmarksite(document.title, 'http://mylink.si');" />
                                        <!--[if IE]><input class="button" type="submit" value="Set as homepage" onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://mylink.si');" /><![endif]-->
                                    </p>
                                    <br />
                                    <img src="images/mail.png" alt="mail" title="e-mail" style="border: 0px;" />
                                <?php
                            break;
                            case "register":
                                ?>
                                    <h1>[ Register ]</h1>
                                <?php
								register();
                            break;
                            case "fb_registration_process":
                                require_once("include/fb_registration_process.php");
                            break;
                            case "register_facebook":
                                require_once("include/register_facebook.php");
                            break;
                            case "viewMap":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ View Folder ]</h1>
                                    
                                <?php
                                if($_GET["mapId"]) {
                                    $map_id = clean($_GET["mapId"]);
                                    ?>
                                    <table cellpadding="0" cellspacing="4" id="tableLinksHome">
                                    <?php
    								vsiLinki($_SESSION["portal_id"], 2, $map_id);
                                    ?>
                                    </table>
                                    <?php
                                }
                                
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
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td><img src="images/wallpapers/Wallpaper1Small.png" alt="Wallpaper 1" title="Preview" /></td>
                                                <td>
                                                    <h3> </h3>
                                                    <ul>
                                                        <li><a href="images/wallpapers/wallpaper1-1024x768.jpg" target="_blank">1024x768</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1280x1024.jpg" target="_blank">1280x1024</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1440x900.jpg" target="_blank">1440x900</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1600x1200.jpg" target="_blank">1600x1200</a></li>
                                                        <li><a href="images/wallpapers/wallpaper1-1920x1080.jpg" target="_blank">1920x1080</a></li>
                                                    </ul>
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
                                    <h1>[ Add Folder ]</h1>
                                    <p class="pText">Here you can add a folder to organize your links in categories.</p>
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
                                    <h1>[ Manage Folders ]</h1>
                                    <p class="pText">Folders are organizational units by which you can organize your links. Each link can be assigned to one folder.</p>
                                    <p class="pText">Manage your folders:</p>
                                    <table cellpadding="0" cellspacing="0" class="tableLinks2" style="width: 250px;">
                                    <tr>
                                        <td class="tdLinkHeadder">Folder name</td>
                                        <td class="tdLinkHeadder">Delete</td>
                                    </tr>
                                <?php
								allMaps();
                                ?>
                                    </table>
                                    <p class="pSubtitles"><?php echo warning("Deleting a folder will also delete its links!"); ?></p>
                                    <table cellpadding="0" cellspacing="0" class="tableLinks2" width="500">
                                    <tr>
                                        <td class="tdLinkHeadder">Title</td>
                                        <td class="tdLinkHeadder">Domain</td>
                                        <td class="tdLinkHeadder">Folder</td>
                                        <td class="tdLinkHeadder">Home page</td>
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
                                  
                                    <h2>Users:</h2>
                                        <table cellpadding="0" cellspacing="0" class="tableLinks2" id="pieData" width="400">
										<thead>
											<tr>
												<th scope="col" class="tdLinkHeadderStats">Men</th>
												<th scope="col" class="tdLinkHeadderStats">Women</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" class="tdLink1"><span class="spanDomainCount"><?php echo usersNum(2); ?></span></td>
												<td scope="row" class="tdLink1"><span class="spanDomainCount"><?php echo usersNum(3); ?></span></td>
											</tr>
                                        </tbody>
										</table>
                               
                                        <h2>Top 10 domains:</h2>
										<table cellpadding="0" cellspacing="0" class="tableLinks2" id="data">
											<?php linksStats(); ?>
											<tr>
												<td colspan="10"><canvas id="graph"></canvas></td>
											</tr>
										</table>
										
										
										<div class="bluff-tooltip">
											<span style="color: #abcdef;"></span>
										</div>
										<script type="text/javascript">
											var g = new Bluff.Bar('graph', '750x320');
											
											//***TEME***
											//g.theme_keynote();
											g.theme_37signals();
											//g.theme_rails_keynote();
											//g.theme_odeo();
											//g.theme_pastel();
											//g.theme_greyscale();
											//***************
							 				g.set_theme({
												colors: ['#060']
											});
											g.title = 'Top 10 domains';
											g.hide_legend = true;
											g.marker_font_size = 10;
											g.bar_spacing = 0.5;
											g.tooltips = true;
											g.data_from_table('data', {orientation: 'cols'});
											g.draw();
										</script>
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
                                    <br /><br /><br />
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
                                <table cellpadding="0" cellspacing="0" class="tableLinks">
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
                            case "safe":
                                ?>
                                    <h1>[ Why secured connection? ]</h1>
                                    
                                    <p class="pText">You have probably heard a lot about safety on the web lately.</p>
                                    <p class="pText">Secured connecion uses an Hypertext Transfer Protocol Secure (HTTPS) which is a combination of the Hypertext Transfer Protocol (HTTP) with SSL/TLS protocol to provide encrypted communication and secure identification of a network web server. HTTPS connections are often used for payment transactions on the World Wide Web and for sensitive transactions in corporate information systems.</p>
                                    <p class="pText">I know that this may seem as some random jibrish to you, but it really protects your user data. To make this HTTPS work propperly, you have to pay quite a large fee to get the right certificate for my server. That is why the browser warns you, that this site cannot be trusted, because of bad certificates. You can add an exception, so this wont bother you.</p>
                                    <p class="pText">So... This secured connection is in every way better that normal HTTP protocol. It doesn't send uncoded data through the web, so sniffers and hackers can't see your password in plain text.</p>
                                    <p class="pText">
                                        Warnings provided by three of the most used browsers:<br />
                                        <a href="#" id="ieErrorShow">Internet explorer</a> |
                                        <a href="#" id="chromeErrorShow">Google Chrome</a> |
                                        <a href="#" id="firefoxErrorShow">Mozilla Firefox</a>
                                    </p>
                                    <p class="pText">
                                        <div id="ieError"><img src="/images/https/ieHTTPS.png" alt="Internet Explorer error" title="Internet Explorer error" /></div>
                                        <div id="chromeError"><img src="/images/https/chromeHTTPS.png" alt="Google Chrome error" title="Google Chrome error" /></div>
                                        <div id="firefoxError"><img src="/images/https/firefoxHTTPS.png" alt="Mozilla Firefox error" title="Mozilla Firefox error" /></div>
                                    </p>
                                <?php
                            break;
                            case "manage":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                <h1>[ Control Panel ]</h1>
                                <br />
                                <br />
                                <table cellpadding="0" cellspacing="0" id="tableLinksHome">
                                    <tr>
                                        <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" onclick="document.location.href = 'index.php?page=allMaps';">
                                            <p class="pLinkName">Links</p>
                                            <p class="pLinkDomain">Manage your links</p>
                                            <p class="pLinkUrl">Set your home page, divide links into folders, ...</p>
                                        </td>
                                        <td>&nbsp; &nbsp;</td>
                                        <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" onclick="document.location.href = 'index.php?page=manageLinks';">
                                            <p class="pLinkName">Home Page</p>
                                            <p class="pLinkDomain">Manage your Home Page</p>
                                            <p class="pLinkUrl">Change the order of links on your Home Page, delete, ...</p>
                                        </td>
                                        <td>&nbsp; &nbsp;</td>
                                        <td class="tdLinkHome tdLinkHome<?php echo rand(1,6);?>" onclick="document.location.href = 'index.php?page=manageAcc';">
                                            <p class="pLinkName">Account</p>
                                            <p class="pLinkDomain">Manage your account</p>
                                            <p class="pLinkUrl">Change your mail, password, ...</p>
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
                            <table cellpadding="0" cellspacing="4" id="tableLinksHome">
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
                                            <a href="index.php?page=register">Register</a> | <a href="https://mylink.si">Secured connection</a>[<a href="index.php?page=safe">?</a>] <!--<fb:login-button perms="email">Login</fb:login-button>-->
                                        </td>
                                    </tr>
								</table>
                            </form>
                            <br /><br />
                            <?php
                        }
                    } 
                    if(isset($_GET["izbrisiLink"])) { 
                        izbrisiLink($_SESSION["portal_id"]);
                    }
					?>
            </td>
        </tr>
    </table>
	<?php if($_SESSION["portal_status"] == 1) { ?>
	<div id="folders">
		<ul class="innerMenu">
			<?php menuMaps(); ?>   
		</ul>
	</div>
	<div id="charmsBottomActivate">Swipe in</div>
	<div id="charmsBottom">
		<?php defaultBrowser($_SESSION["portal_id"]); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input class="button" id="addLinkButton" type="submit" value="Add Link" />
		<input class="button" id="addMapButton" type="submit" value="Add Folder" />
		<input class="button" id="sendLinkButton" type="submit" value="Send Link" />
	</div>
	<?php } ?>
	<script type="text/javascript">
	//** LINKI **
	setTimeout(function() {$('.tdLinkHome').slideDown();},600);
	//****
	</script>
</body>
</html>
<?php } ?>