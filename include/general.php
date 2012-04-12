<?php
function ura($ura) {
	$ura = split(":", $ura);
	return $ura[0] . ":" . $ura[1];
}

function datum($izbira, $datum) {
	if($izbira == 1) {//spreminja iz yyyy-mm-dd v d. mesec leto
		$dat = split("-", $datum);
		return $dat[2] . ". " . datum(3, $dat[1]) . " " . $dat[0];
	}
	elseif($izbira == 2) {//spreminja iz yyyy-mm-dd v d. mes leto
		$dat = split("-", $datum);
		return $dat[2] . ". " . $dat[1] . ". " . $dat[0];
	}
	elseif($izbira == 3) {// spremeni mm v mesec
		$datum = str_replace("01", "januar", $datum);
		$datum = str_replace("02", "februar", $datum);
		$datum = str_replace("03", "marec", $datum);
		$datum = str_replace("04", "april", $datum);
		$datum = str_replace("05", "maj", $datum);
		$datum = str_replace("06", "junij", $datum);
		$datum = str_replace("07", "julij", $datum);
		$datum = str_replace("08", "avgust", $datum);
		$datum = str_replace("09", "september", $datum);
		$datum = str_replace("10", "oktober", $datum);
		$datum = str_replace("11", "november", $datum);
		$datum = str_replace("12", "december", $datum);
		
		return $datum;
	}
}

function alert($text) {
	?>
	<script language="javascript">
		alert("<?php echo $text; ?>");
	</script>
	<?php
}

function history($h) {
	?>
		<script language="javascript">
			history.go(<?php echo $h; ?>);
		</script>
	<?php
}

function refresh() {
    ?>
    <script language="javascript">
		window.location.reload( false );
	</script>
    <?php
}

function redirect($url){
    ?>
    <script type="text/javascript">
        setTimeout("window.location='<?php echo $url; ?>'",100);
    </script>
    <?php
}

function clean($dirty) {
	$clean = mysql_real_escape_string($dirty);
	htmlentities($clean);
	return $clean;
}

function vsiLogi() {	
	$mydb = mysql_connect("localhost", "root", "psxdxn83");
	mysql_select_db("seylla", $mydb);
	$select = "SELECT * FROM loging ORDER BY datum DESC, ura DESC LIMIT 100";
	$query = mysql_query ($select, $mydb);
	$i = 1;
	if(mysql_num_rows($query) < 1) {
		?>
		<tr>
            <td class="tdPodatki<?php echo $i; ?>">Ne obstaja
            </td>
            <td class="tdPodatki<?php echo $i; ?>">Ne obstaja
            </td>
            <td class="tdPodatki<?php echo $i; ?>">Ne obstaja
            </td>
            <td class="tdPodatki<?php echo $i; ?>">Ne obstaja
            </td>
            <td class="tdPodatki<?php echo $i; ?>">Ne obstaja
            </td>
        </tr>
        <?php
	}
	else{
		while($vrstica = mysql_fetch_array($query)) {
			enLog($vrstica["id"], $i);
			if($i == 1) $i = 2;
			else $i =1;
		}
	}
}


function enLog($id, $i) {
	$mydb = mysql_connect("localhost", "root", "psxdxn83");
	mysql_select_db("seylla", $mydb);
	$select = "SELECT * FROM loging WHERE id = " . $id;
	$query = mysql_query ($select, $mydb);
	
	if(mysql_num_rows($query) == 1) {
		$vrstica = mysql_fetch_array($query);
		?>
        <tr>
			<td class="tdPodatki<?php echo $i; ?>"><?php echo datum(1, $vrstica["datum"]); ?>
			</td>
			<td class="tdPodatki<?php echo $i; ?>"><?php echo ura($vrstica["ura"]); ?>
			</td>
			<td class="tdPodatki<?php echo $i; ?>"><?php echo $vrstica["nick"]; ?>
			</td>
            <td class="tdPodatki<?php echo $i; ?>"><?php echo $vrstica["akcija"]; ?>
			</td>
             <td class="tdPodatki<?php echo $i; ?>"><?php echo $vrstica["ip"]; ?>
			</td>
		</tr>
		<?php
	}
	else {
		echo "Ne obstaja";
	}
	mysql_close($mydb);
}

function zalogiraj($akcija) {
	$mydb1 = mysql_connect("localhost", "root", "psxdxn83");
	mysql_select_db("seylla", $mydb1);
	$datum1 = date("Y") . "-" . date("m") . "-" . date("d");
	$insert = "INSERT INTO loging (nick, ip, datum, ura, akcija) VALUES ('" . clean($_SESSION["portal_user"]) . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $datum1 . "', '" . date("G:i") . "', '" . $akcija . "')";
	$query1 = mysql_query ($insert, $mydb1);
	mysql_close($mydb1);
}

function warning($sporocilo) {
	$novoSporocilo = "
	<br />
	<table cellpadding=\"0\" cellspacing=\"0\" class=\"warning\">
		<tr>
			<td valign=\"top\" width=\"24\">
				<img src=\"../images/warning.png\" alt=\"Warning!\" title=\"Warning!\" style = \"border = 0px;\" />
			</td>
			<td>
				" . $sporocilo . "
			</td>
		</tr>
	</table>
	<br />
	";
	return $novoSporocilo;
}


function error($sporocilo) {
	$novoSporocilo = "
    <br />
	<table cellpadding=\"0\" cellspacing=\"0\" class=\"error\">
		<tr>
			<td valign=\"top\" width=\"24\">
				<img src=\"../images/error.png\" alt=\"Error!\" title=\"Error!\" style = \"border = 0px;\" />
			</td>
			<td>
				" . $sporocilo . "
			</td>
		</tr>
	</table>
	";
	return $novoSporocilo;
}

function info($sporocilo) {
	$novoSporocilo = "
	<br />
	<table cellpadding=\"0\" cellspacing=\"0\" class=\"info\">
		<tr>
			<td valign=\"top\" width=\"24\">
				<img src=\"../images/info.png\" alt=\"Info!\" title=\"Info!\" style = \"border = 0px;\" />
			</td>
			<td>
				" . $sporocilo . "
			</td>
		</tr>
	</table>
	<br />
	";
	return $novoSporocilo;
}
/*
function getTitle($url) {
	$fh = fopen($url, "r");
	$str = fread($fh, 7500);
	fclose($fh);
	$str2 = strtolower($str);
	$start = strpos($str2, "<title>")+7;
	$len = strpos($str2, "</title>") - $start;
	return substr($str, $start, $len);
}
*/

function selectBrowser($id) {
    global $config;
    global $mydb;
    if(isset($_POST["browserSent"])) {
        $brow = clean($_POST["browser"]);
        $update = "UPDATE users SET fav_search = " . $brow . " WHERE id = " . $id;
        $mydb->query($update);
        if($mydb->result())
            echo info("Your default search engine was successfully chosen!");
        else
            echo error("There was an error regarding the database. Please try again later...<br />");
    } else {
        ?>
        <form method="post" action="<?php $PHP_SELF; ?>">
                <input type="radio" name="browser" value="1" /> Google<br />
                <input type="radio" name="browser" value="2" /> Bing<br />
                <input type="radio" name="browser" value="3" /> Najdi.si<br /><br />
                <input class="button" type="submit" name="browserSent" value="Confirm" />
        </form>
        <?php
    }
}

function defaultBrowser($id) {
    global $config;
    global $mydb;
    $select = "SELECT fav_search FROM users WHERE id = " . $id;
    $mydb->query($select);
    if($mydb->recno() == 1) {
        $result = $mydb->row();
		if($result["fav_search"] == 0) {
			echo info("You can select your default search engine <a href='index.php?page=search' title='Choose your default search engine'>here</a>.");
		} else if($result["fav_search"] == 1) {
			?>
				<form method="get" action="http://www.google.com/search" target="_blank">
					<input class="field" type="text" name="q" />
					<input class="button" type="submit" value="Google Search" />
				</form>
			<?php
		} else if($result["fav_search"] == 2) {
			?>
				<form method="get" action="http://www.bing.com/search" target="_blank">
					<input class="field" type="text" name="q" />
					<input class="button" type="submit" value="Bing Search" />
				</form>
			<?php
		} else if($result["fav_search"] == 3) {
			?>
				<form name="form" method="get" action="http://www.najdi.si/search.jsp" onsubmit="target='_new'; return true;" style="margin: 0pt; padding: 0pt;">
					<input name="q" type="text" class="field" />
					<input class="button" type="submit" value="Najdi.si Search" />
					<input name="st" value="custom" checked="checked" type="hidden" />
					<input name="inenc" value="UTF-8" type="hidden" />
				</form>
			<?php
		}
    } else {
        echo error("Could not get your default search engine!");
    }
}

function skrci($string) {
    if(strlen($string) > 30) {
        $string = substr($string, 0, 30) . "...";
        return $string;
    }
    else
        return $string;
}
?>