<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
global $config;
global $mydb;
session_start();
header("Chache-control: private");
if(!isset($_SESSION["portal_status"]) || $_SESSION["portal_priv"] < 2) {
    $_SESSION["portal_status"] = 0;
    redirect("../index.php");
}
// Includes
require_once("../include/general.php");
require_once("../include/register.php");
require_once("../include/link.php");
require_once("../include/stats.php");
require_once("../include/notes.php");
include "../config.php";
include "../include/db.php";
//

$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
$mydb1 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);

// log off, remove cookie
if(isset($_GET["odjava"])) {
	$_SESSION["portal_status"] = 0;
	if(isSet($_COOKIE[$cookie_name])) {
		// remove 'site_auth' cookie
		setcookie ($cookie_name, '', time() - $cookie_time);
	}
}
//
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Language" content="SI" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="revisit-after" content="15 days" />
    <link rel="stylesheet" href="../styles/orig.css" type="text/css" media="all" />
    <!--[if IE]> <link rel="stylesheet" type="text/css" href="../styles/orig_ie.css" /> <![endif]--> 
    <link rel="shortcut icon" href="../images/icons/home.ico" />
    <script type="text/javascript" src="../include/scripts.js"></script>
	<title>
    	<?php
		global $config;
		echo  $config["siteTitle"];
		?>
    </title>
</head>
<body>
	<div id="divTopRob">
    	<div id="divTopMeni">
            <!-- Main menu -->      
            <div id="divLoginApplied">
				Welcome to administration <?php echo $_SESSION["portal_user"]; ?>! &nbsp; [ <a href="?odjava" title="Log Off">Log Off</a> ]
            </div>
            <div id="divUlPageNavHolder">
                <ul id="ulPageNav">
                    <li class="divMeniItem" onclick="document.location.href='index.php';">Home</li>
                </ul>
            </div>
            <!-- Main content holder -->
            <div id="divContent0">
            <div id="divContentMeni">
            	<h2>Meni</h2>
                <ul>
                    <li><a href="index.php">Home</a></li>
                </ul>
                
                <ul>
                	<li><a href="?page=manageUsers">Manage users</a></li>
                </ul>
                <ul>
                    <li><a href="../index.php" title="Log Off">Front page</a></li>
                    <li><a href="?odjava" title="Log Off">Log Off</a></li>
                </ul>
                <br />
            </div>
            <div id="divContent1">
					<?php
                    if(isset($_GET["page"])) {
                        switch($_GET['page']) {
                            case "manageAcc":
                                if($_SESSION["portal_status"] != 1) {
                                    alert("You are not logged in!");
                                    history(-1);
                                    break;
                                }
                                ?>
                                    <h1>[ Manage your account ]</h1>
                                <?php
        						editUser($_SESSION["portal_id"]);
                            break;
                            case "manageUsers":
                                ?>
                                <h1>[ Manage portal users ]</h1>
                                <table id="tableUsers" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="tdLinkHeadder">Manage</td>
                        				<td class="tdLinkHeadder">Name</td>
                                        <td class="tdLinkHeadder">Surname</td>
                                        <td class="tdLinkHeadder">Email</td>
                                        <td class="tdLinkHeadder">Role</td>
                                        <td class="tdLinkHeadder">Gender</td>
                                        <td class="tdLinkHeadder">Logins</td>
                                        <td class="tdLinkHeadder">Last Login</td>
                                        <td class="tdLinkHeadder">WWW Searcher</td>
                                    </tr>
                                <?php
        						vsiUserji();
                                ?>
                                </table>
                                <?php
                                if(isset($_GET["manageAcc"]))
                                    editUserAdmin($_GET["manageAcc"]);
                                if(isset($_GET["izbrisiUser"]))
                                    izbrisiUser($_GET["izbrisiUser"]);
                            break;
                        }
                    } 
                    else {
						?>
                        <h1>[ Statistics ]</h1>
                        <h2>Users:</h2>
                        <p class="pText">
                        Currently registered users: <b><?php echo usersNum(1); ?></b>
                            <ul>
                                <li><b><?php echo usersNum(2); ?></b> men</li>
                                <li><b><?php echo usersNum(3); ?></b> women</li>
                            </ul>
                        </p>
                        
                        <h2>All links:</h2>
                        <p class="pText">Current number of used links: <b><?php echo usersNum(4); ?></b></p>
                        
                        <h2>Top 10 domains:</h2>
                        <p class="pText">
                            <ol>
                                <?php linksStats(); ?>
                            </ol>
                            
                        </p>
                        <?php  
                    }
					?>
    			</div>
                <div id="divCopyright"><!--[if IE]>[ <a href="" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://mylink.si');">Set myLink as my home page!</a> ] <![endif]-->
 &nbsp;[&nbsp;&copy; myLink &amp; co. 2011 &nbsp;] &nbsp;[ <a href="" onclick="bookmarksite(document.title, 'http://mylink.si');">Add myLink to favourites!</a> ]</div>
    		</div>
        </div>
        <!-- Small logo top right -->
        <!--<div id="divLogoTop"><img src="images/logoMali.png" title="Logo" height="30" width="80" alt="LogoBeli" />[ myLink ]</div>  -->     
    </div> 
</body>
</html>