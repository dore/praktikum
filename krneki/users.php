<?php
function vsiUserji() {	
	global $config;
    global $mydb;
	$select = "SELECT * FROM krneki ORDER BY sifra DESC";
    $mydb->query($select);
	$i = 1;
	if($mydb->recno() < 1) {
		?>
		<tr>
			<td class="tdLink<?php echo $i; ?>">Ne obstaja!</td>
		</tr>
        <?php
	} else {
		while($vrstica = $mydb->row()) {
			enUser($vrstica["id"], $i);
			if($i == 1) $i = 2;
			else $i =1;
		}
	}
}

function enUser($id, $i) {
	global $config;
    global $mydb1;
	$select = "SELECT * FROM krneki WHERE id = " . $id;
	$mydb1->query($select);
	if($mydb1->recno() == 1) {
		$vrstica = $mydb1->row();
		?>
        	<tr class="tdLink<?php echo $i; ?>">
				<td><?php echo $vrstica["sifra"]; ?></td>
				<td><?php echo $vrstica["ime"]; ?></td>
				<td><?php echo $vrstica["dm"]; ?></td>
				<td><?php echo $vrstica["ulica"]; ?></td>
				<td><?php echo $vrstica["kraj"]; ?></td>
				<td><?php echo $vrstica["posta"]; ?></td>
				<td><?php echo $vrstica["drzava"]; ?></td>
				<td><?php echo datum(1, $vrstica["rojstvo"]); ?></td>
				<td><?php echo $vrstica["telefon"]; ?></td>
				<td><?php echo $vrstica["sprememba"]; ?></td>
				<td><a href="?izbrisi=<?php echo $vrstica["id"]; ?>">Izbriši</a> | <a href="?page=uredi&id=<?php echo $vrstica["id"]; ?>">Uredi</a> | <a href="?page=kopiraj&id=<?php echo $vrstica["id"]; ?>">Kopiraj</a></td>
            </tr>
		<?php
	}
	else {
		echo "Ni podatkov!";
	}
}

function izbrisiUser($user_id) {
    global $config;
    global $mydb;
	$delete = "DELETE FROM krneki WHERE id =  " . $user_id;
	$mydb->query($delete);
	if($mydb->result()) {
		echo info("Uspešno izbrisan uporabnik!");
	} else {
		echo error("Brisanje uporabnika ni uspelo!");
	}
}

function user($userId, $option) {
	global $config;
	global $mydb;
	
	if($_POST['send']){
		$sifra = clean($_POST['sifra']);
		$ime = clean($_POST['ime']);
		$dm = clean($_POST['dm']);
		$ulica = clean($_POST['ulica']);
		$kraj = clean($_POST['kraj']);
		$posta = clean($_POST['posta']);
		$drzava = clean($_POST['drzava']);
		$rojstvo = clean($_POST['rojstvo']);
		$telefon = clean($_POST['telefon']);
		
		$errors = array();
        $errMesssage = "";
		
			if(!$sifra){
				$errors[] = "Name is not defined!";
                $errMesssage = $errMesssage . "Sifra ni definirana!<br />";
			}
						
			if(!$ime){
				$errors[] = "Surnameame is not defined!";
                $errMesssage = $errMesssage . "Ime in priimek nista definirana!<br />";
			}
			
			if(!$ulica){
				$errors[] = "Password is not defined!";
                $errMesssage = $errMesssage . "Ulica ni definirana!<br />";
			}
			
			if(!$kraj){
				$errors[] = "Confirmation password is not defined!";
				$errMesssage = $errMesssage . "Kraj ni definiran!<br />";
			}
			
			if(!$posta){
				$errors[] = "E-mail is not defined!";
                $errMesssage = $errMesssage . "Pošta ni definirana!<br />";
			}
			
			if(!$drzava){
				$errors[] = "Gender is not defined!";
                $errMesssage = $errMesssage . "Država ni definirana!<br />";
			}
			
			if(!$rojstvo){
				$errors[] = "Name can only contain numbers and letters!";
				$errMesssage = $errMesssage . "Rojstvo ni definirano!<br />";
			}
			
			if(!$telefon){
				$errors[] = "Surname can only contain numbers and letters!";
				$errMesssage = $errMesssage . "Telefonska številka ni definirana!<br />";
			}
									
			if(count($errors) > 0){
                echo error($errMesssage);
				echo "<br /><a href='index.php'>Domov</a>";
			}else {
				if($option == 1) {
					$sql = "UPDATE krneki SET sifra = " . $sifra . ", ime = '" . $ime . "', dm = " . $dm . ", ulica = '" . $ulica . "', kraj = '" . $kraj . "', posta = '" . $posta . "', drzava = '" . $drzava . "', rojstvo = '" . $rojstvo . "', telefon = '" . $telefon . "', sprememba = CURRENT_TIMESTAMP WHERE id = " . $userId;
					$mydb->query($sql);
					if($mydb->result())
						echo info("Uspešno ste uredili delavca!") . "<br /><a href='index.php'>Domov</a>";
					else 
						echo error("Nekaj je narobe z bazo!"); 
				} else {
					$sql = "INSERT INTO krneki (`id`, `sifra`, `ime`, `dm`, `ulica`, `kraj`, `posta`, `drzava`, `rojstvo`, `telefon`, `sprememba`) VALUES (NULL, " . $sifra . ", '" . $ime . "', " . $dm . ", '" . $ulica . "', '" . $kraj . "', '" . $posta . "', '" . $drzava . "', '" . $rojstvo . "', '" . $telefon . "', CURRENT_TIMESTAMP);";
					$mydb->query($sql);
					if($mydb->result())
						if($option == 3)
							echo info("Uspešno ste dodali delavca!") . "<br /><a href='index.php'>Domov</a>";
						else
							echo info("Uspešno ste kopirali delavca!") . "<br /><a href='index.php'>Domov</a>";
					else 
						echo error("Nekaj je narobe z bazo!"); 
				}
			}
	}
    else {
	   $select = "SELECT * FROM krneki WHERE id = " . $userId;
       $mydb->query($select);
       if($mydb->recno()) $vrstica = $mydb->row();
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<table align="center">
				<tr>
					<td>Šifra:</td>
					<td><input type="text" name="sifra" size="20" class="field" value="<?php if($option == 1) echo $vrstica["sifra"]; else if($option == 2) echo "NASTAVI ŠIFRO"; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Ime in priimek:
					</td>
					<td><input type="text" name="ime" size="20" class="field" value="<?php  if($option != 3) echo $vrstica["ime"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Delavno mesto:
					</td>
					<td><input type="text" name="dm" size="20" class="field" value="<?php  if($option != 3) echo $vrstica["dm"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Ulica:
					</td>
					<td><input type="text" name="ulica" size="20" class="field" value="<?php  if($option != 3) echo $vrstica["ulica"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Kraj:
					</td>
					<td><input type="text" name="kraj" size="20" class="field" value="<?php  if($option != 3) echo $vrstica["kraj"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Pošta:
					</td>
					<td><input type="text" name="posta" size="20" class="field" value="<?php  if($option != 3) echo $vrstica["posta"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Država:
					</td>
					<td><input type="text" name="drzava" size="20" class="field" value="<?php if($option != 3) echo $vrstica["drzava"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Rojstni datum:
					</td>
					<td><input type="text" name="rojstvo" size="20" class="field" value="<?php if($option != 3) echo $vrstica["rojstvo"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Telefonska številka:
					</td>
					<td><input type="text" name="telefon" size="20" class="field" value="<?php if($option != 3) echo $vrstica["telefon"]; ?>" /> 
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input style="width: 100px;" type="submit" name="send" value="OK" class="button">&nbsp;
					<input style="width: 70px;" type="reset" name="reset" value="Reset" class="button" />&nbsp; &nbsp; &lowast;Vsa polja so obvezna!
					</td>
				</tr>
			</table>
			</form>
		<?php
	}
}

function starost() {
	global $config;
    global $mydb;
	
	$select = "SELECT * FROM krneki";
    $mydb->query($select);
	$i = 1;
	if($mydb->recno() < 1) {
		echo "Podatki ne obstajajo!";
	} else {
		$i = 0;
		$skupnaStarost = 0;
		$letos = date("Y");
		
		while($vrstica = $mydb->row()) {
			$letoRojstva = explode("-", $vrstica["rojstvo"]);
			$skupnaStarost += $letos - $letoRojstva[0];
			$i++;
		}
		echo "<div style='margin-left: 30px; font-size: 25px;'>" . round($skupnaStarost / $i, 2) . " let</div>";
	}
}

function tedenskiIzpis() {
	global $config;
    global $mydb;
	
	$date = date('Y') . "-". date('m') . "-" . date('d') . " " . date('H') . ":" . date('i') . ":" . date('s');
	
	$select = "SELECT * FROM krneki WHERE sprememba >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY sifra DESC";
    $mydb->query($select);
	$i = 1;
	if($mydb->recno() < 1) {
		echo "<div style='margin-left: 30px; font-size: 25px;'>Ni podatkov!<br />";
		echo $select . "</div>";
	} else {
		echo "<ul>";
		while($vrstica = $mydb->row()) {
			echo "<li>" . $vrstica["sifra"] . ", " . $vrstica["ime"] ."</li>";
		}
		echo "</ul>";
	}
}
?>
