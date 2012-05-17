<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php
require_once("../include/general.php");     
global $config;
global $mydb;

// Includes
require_once("users.php");
include "../config.php";
include "../include/db.php";
//**************

$mydb = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
$mydb1 = new Db($config["dbName"], $config["dbHost"], $config["dbUser"], $config["dbPass"]);
if(isset($_GET["izbrisi"])) {
	izbrisiUser(clean($_GET["izbrisi"]));
	redirect("index.php");
}

function getData($username) {
	if(@file("data/" . $username . ".txt")) {
		$file = file("data/" . $username . ".txt");
		echo trim($file[0]) . " " . trim($file[1]) . " " . trim($file[2]);
	} else {
		file_put_contents("data/" . $username . ".txt", "0\r\n0\r\n0");
		$file = file("data/" . $username . ".txt");
		echo trim($file[0]) . " " . trim($file[1]) . " " . trim($file[2]);
	}
}

?>
<link rel="stylesheet" href="../Style/default.css" type="text/css" media="all" />
</head>

<body>
<h1 style="margin: 30px 30px 0px 20px; font-size: 33px;">Delavci</h1>
<a href="index.php">Domov</a> | <a href="?page=dodaj">Dodaj delavca</a> | <a href="?page=starost">Povprečna starost</a> | <a href="?page=izpis">Tedenski izpis</a>
	<?php 
		switch($_GET['page']) {
			case "izpis":
				?><h2>Poročilo tedenskih aktivnosti</h2><?php
				tedenskiIzpis();
			break;
			case "starost":
				?><h2>Povprečna starost delavcev</h2><?php
				starost();
			break;
			case "dodaj":
				?><h2>Dodaj delavca</h2><?php
				user(clean($_GET["id"]), 3);
			break;
			case "uredi":
				?><h2>Uredi delavca</h2><?php
				user(clean($_GET["id"]), 1);
			break;
			case "kopiraj":
				?><h2>Kopiraj delavca</h2><?php
				user(clean($_GET["id"]), 2);
			break;
			default:
				?>
					<table class="tableLinks" style="margin: 0px 0px 0px 20px; width: 95%;">
						<tr style="background-color: green;">
							<td>Šifra</td>
							<td>Ime in priimek</td>
							<td>Delovno mesto</td>
							<td>Ulica</td>
							<td>Kraj</td>
							<td>Pošta</td>
							<td>Država</td>
							<td>Rojstni datum</td>
							<td>Telefon</td>
							<td>Zadnja sprememba</td>
							<td>Možnosti</td>
						</tr>
						<?php
						vsiUserji();
						?>
					</table>
					<br />
				<?php
			break;
		}
	?>
</body>
</html>