<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once("../include/general.php");     

$data;
$i = 0;

function getData($fileName) {
	global $data;
	global $i;
	if(@file($fileName)) {
		$file = file($fileName);
		$data = explode(",", $file[0]);
		$i = 0;
		while($data[$i]) {
			if($j == 1) $j = 2;
			else $j =1;
			?>
			<tr class="tdLink<?php echo $j; ?>">
				<td><?php echo $i; ?></td>
				<td><?php echo $data[$i]; ?></td>
			</tr>
			<?php
			$i++;
		}
	}
}

function avg() {
	global $data;
	global $i;
	$skupna = 0;
	
	for($j = 0; $j <= $i; $j++)
		$skupna += $data[$j];	
	return ($skupna+160) / ($i+1);
}

function stdOdklon() {
	global $data;
	global $i;
	$povp = avg();
	$skupna = 0;
	
	for($j = 0; $j <= $i; $j++)
		$skupna += ($data[$j] - $povp)*($data[$j] - $povp);
	$skupna = $skupna / ($i+1);
	
	return round(sqrt($skupna),2);	
}

?>
<html>
<head>
<link rel="stylesheet" href="../Style/default.css" type="text/css" media="all" />
</head>
<body>
	<h1 style="margin: 30px 30px 0px 20px; font-size: 33px;">Povprečna vrednost in standardni odklon</h1>
	<table class="tableLinks" style="margin: 0px 0px 0px 20px; width: 40%;">
		<tr style="background-color: green;">
			<td style="width: 10%;">index</td>
			<td>Data</td>
		</tr>
		<?php
		getData("data.txt");
		?>
	</table>
	<br />
	<div style='margin-left: 30px; font-size: 25px;'>Povprečna vrednost podatkov: <?php echo avg(); ?><br /></div>
	<div style='margin-left: 30px; font-size: 25px;'>Standardni odklon: <?php echo stdOdklon(); ?><br /></div>
</body>
</html>