<?php
function register() {
	global $config;
	global $mydb;
	
	if($_POST['send']){
		$name = clean($_POST['name']);
		$surname = clean($_POST['surname']);
		$password = clean($_POST['password']);
		$confirm = clean($_POST['password2']);
		$email = clean($_POST['mail']);
		$gender = clean($_POST['gender']);
		
		$errors = array();
        $errMesssage = "";
		
			if(!$name){
				$errors[] = "Name is not defined!";
                $errMesssage = $errMesssage . "Name is not defined!<br />";
			}
						
			if(!$surname){
				$errors[] = "Surnameame is not defined!";
                $errMesssage = $errMesssage . "Surnameame is not defined!<br />";
			}
			
			if(!$password){
				$errors[] = "Password is not defined!";
                $errMesssage = $errMesssage . "Password is not defined!<br />";
			}
			
			if($password){
				if(!$confirm){
					$errors[] = "Confirmation password is not defined!";
                    $errMesssage = $errMesssage . "Confirmation password is not defined!<br />";
				}
			}
			
			if(!$email){
				$errors[] = "E-mail is not defined!";
                $errMesssage = $errMesssage . "E-mail is not defined!<br />";
			}
			
			if($gender == "0"){
				$errors[] = "Gender is not defined!";
                $errMesssage = $errMesssage . "Gender is not defined!<br />";
			}
			/*
			if($name){
				if(!ctype_alnum($name)){
					$errors[] = "Name can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Name can only contain numbers and letters!<br />";
				}
			}
			
			if($surname){
				if(!ctype_alnum($surname)){
					$errors[] = "Surname can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Surname can only contain numbers and letters!<br />";
				}
			}
			*/
			if($password && $confirm){
				if($password != $confirm){
					$errors[] = "Passwords do not match!";
                    $errMesssage = $errMesssage . "Passwords do not match!<br />";
				}
			}
			
			if($email){
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				if(!preg_match($checkemail, $email)){
					$errors[] = "E-mail is not valid, must be name@server.tld!";
                    $errMesssage = $errMesssage . "E-mail is not valid, must be name@server.tld!<br />";
				}
			}
			
			if($email){
				$sql = "SELECT * FROM users WHERE mail='" . $email . "'";
				$mydb->query($sql);
				
					if($mydb->recno() > 0){
						$errors[] = "The e-mail address you supplied is already in use of another user!";
                        $errMesssage = $errMesssage . "The e-mail address you supplied is already in use of another user!<br />";
					}
			}
			
			if(count($errors) > 0){
				/*foreach($errors AS $error){
					echo $error;
				}*/
                echo error($errMesssage);
				echo "<br /><a href='?page=register'>Back</a>";
			}else {
				$sql = "INSERT INTO users (name, surname, mail, password, role, gender, logins, last_login, fav_search, active) VALUES ('" . $name . "','" . $surname . "','" . $email . "','" . md5($password) . "', 1,'" . $gender . "', 0, 0000-00-00, 0, 0)";
				$mydb->query($sql);
				if($mydb->result())
					echo info("You have successfully registered as <b>" . $name . " " . $surname . "</b>! <br /><a href='index.php'>Back</a>");
                else 
                    echo error("Error in database!") . "<br /> <a href ='?page=register'>Back</a>";
                        
			}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableRegister">
			<legend>Enter the following information</legend>
	 
			<table>
				<tr>
					<td>Name:
					</td>
					<td><input type="text" name="name" size="20" class="field" /> 
					</td>
				</tr>
				<tr>
					<td>Surname:
					</td>
					<td><input type="text" name="surname" size="20" class="field" /> 
					</td>
				</tr>
				<tr>
					<td>Password:
					</td>
					<td><input type="password" name="password" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>Repeat password:
					</td>
					<td><input type="password" name="password2" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>e-mail:
					</td>
					<td><input type="text" name="mail" size="25" class="field" /> 
					</td>
				</tr>
				<tr>
					<td>Gender:
					</td>
					<td>
						<select name="gender">
							<option value="0" selected>------</option>
							<option value="1"><b>Male</b></option>
							<option value="2"><b>Female</b></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="send" value="Confirm" class="button">&nbsp;
					<input type="reset" name="reset" value="Reset" class="button" />&nbsp; &nbsp; &lowast;All fields are required
					</td>
				</tr>
			</table>
			</fieldset>
			</form>
		<?php
	}
}

function editUser($userId) {
	global $config;
	global $mydb;
	
	if($_POST['send']){
		$name = clean($_POST['name']);
		$surname = clean($_POST['surname']);
		$password = clean($_POST['password']);
		$confirm = clean($_POST['password2']);
		$email = clean($_POST['mail']);
		$gender = clean($_POST['gender']);
		
		$errors = array();
        $errMesssage = "";
		
			if(!$name){
				$errors[] = "Name is not defined!";
                $errMesssage = $errMesssage . "Name is not defined!<br />";
			}
						
			if(!$surname){
				$errors[] = "Surnameame is not defined!";
                $errMesssage = $errMesssage . "Surnameame is not defined!<br />";
			}
			
			if(!$password){
				$errors[] = "Password is not defined!";
                $errMesssage = $errMesssage . "Password is not defined!<br />";
			}
			
			if($password){
				if(!$confirm){
					$errors[] = "Confirmation password is not defined!";
                    $errMesssage = $errMesssage . "Confirmation password is not defined!<br />";
				}
			}
			
			if(!$email){
				$errors[] = "E-mail is not defined!";
                $errMesssage = $errMesssage . "E-mail is not defined!<br />";
			}
			
			if(!$gender){
				$errors[] = "Gender is not defined!";
                $errMesssage = $errMesssage . "Gender is not defined!<br />";
			}
			
			if($name){
				if(!ctype_alnum($name)){
					$errors[] = "Name can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Name can only contain numbers and letters!<br />";
				}
			}
			
			if($surname){
				if(!ctype_alnum($surname)){
					$errors[] = "Surname can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Surname can only contain numbers and letters!<br />";
				}
			}
			
			if($password && $confirm){
				if($password != $confirm){
					$errors[] = "Passwords do not match!";
                    $errMesssage = $errMesssage . "Passwords do not match!<br />";
				}
			}
			
			if($email){
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				if(!preg_match($checkemail, $email)){
					$errors[] = "E-mail is not valid, must be name@server.tld!";
                    $errMesssage = $errMesssage . "E-mail is not valid, must be name@server.tld!<br />";
				}
			}
			
			if($email){
				$sql = "SELECT * FROM users WHERE mail='" . $email . "'";
				$mydb->query($sql);
				$userInfo = $mydb->row();
                    if($userId != $userInfo["id"]) {
    					if($mydb->recno() > 0){
    						$errors[] = "The e-mail address you supplied is already in use of another user!";
                            $errMesssage = $errMesssage . "The e-mail address you supplied is already in use of another user!<br />";
    					}
                    }
			}
			
			if(count($errors) > 0){
				/*foreach($errors AS $error){
					echo $error;
				}*/
                echo error($errMesssage);
				echo "<br /><a href='?page=manageAcc'>Back</a>";
			}else {
				$sql = "UPDATE users SET name = '" . $name . "', surname = '" . $surname . "', mail = '" . $email . "', password = '" . md5($password) . "', role = 1, gender = '" . $gender . "' WHERE id = " . $userId;
				$mydb->query($sql);
				if($mydb->result())
					echo info("You have successfully edited your account!") . "<br /><a href='?page=index.php'>Back</a>";
                else 
                    echo error("There was an error regarding the database! Please try again later!");                
			}
	}
    else {
	   $select = "SELECT * FROM users WHERE id = " . $userId;
       $mydb->query($select);
       if($mydb->recno()) $vrstica = $mydb->row();
		?>
        <p class="pText">You have already logged in <b><?php echo $vrstica["logins"] ?></b> times!</p>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableRegister">
			<legend>Enter the following information</legend>
	 
			<table>
				<tr>
					<td>Name:
					</td>
					<td><input type="text" name="name" size="20" class="field" value="<?php echo $vrstica["name"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Surname:
					</td>
					<td><input type="text" name="surname" size="20" class="field" value="<?php echo $vrstica["surname"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Password:
					</td>
					<td><input type="password" name="password" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>Repeat password:
					</td>
					<td><input type="password" name="password2" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>e-mail:
					</td>
					<td><input type="text" name="mail" size="25" class="field" value="<?php echo $vrstica["mail"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Gender:
					</td>
					<td>
						<select name="gender">
							<option value="1" <?php if($vrstica["gender"] == 1) echo "selected";  ?>><b>Male</b></option>
							<option value="2" <?php if($vrstica["gender"] == 2) echo "selected";  ?>><b>Female</b></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="send" value="Confirm" class="button">&nbsp;
					<input type="reset" name="reset" value="Reset" class="button" />&nbsp; &nbsp; &lowast;All fields are required
					</td>
				</tr>
			</table>
			</fieldset>
			</form>
		<?php
	}
}

function vsiUserji() {	
	global $config;
    global $mydb;
	$select = "SELECT * FROM users ORDER BY last_login DESC";
    $mydb->query($select);
	$i = 1;
	if($mydb->recno() < 1) {
		?>
		<tr>
            	<td class="tdLink<?php echo $i; ?>">Ne obstaja!</td>
				<td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
                <td class="tdLink<?php echo $i; ?>"></td>
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
	$select = "SELECT * FROM users WHERE id = " . $id;
	$mydb1->query($select);
	if($mydb1->recno() == 1) {
		$vrstica = $mydb1->row();
		?>
        	<tr>
            	<td class="tdLink<?php echo $i; ?>" align="right"><a href="?page=manageUsers&izbrisiUser=<?php echo $vrstica["id"]; ?>"><img src="../images/delete.png" border="0" alt="Del" title="Delete" /></a><a href="?page=manageUsers&manageAcc=<?php echo $vrstica["id"]; ?>"><img src="../images/edit.png" border="0" alt="Edit" title="Edit" /></a></td>
				<td class="tdLink<?php echo $i; ?>"><?php echo $vrstica["name"]; ?></td>
                <td class="tdLink<?php echo $i; ?>"><?php echo $vrstica["surname"]; ?></td>
                <td class="tdLink<?php echo $i; ?>"><?php echo $vrstica["mail"]; ?></td>
                <td class="tdLink<?php echo $i; ?>">
                    <?php 
                    if($vrstica["role"] < 2) echo "User";
                    else if($vrstica["role"] > 1) echo "Admin"; 
                    ?>
                </td>
                <td class="tdLink<?php echo $i; ?>">
                    <?php 
                    if($vrstica["gender"] == 1) echo "Male";
                    else if($vrstica["gender"] == 2) echo "Female"; 
                    ?>
                </td>
                <td class="tdLink<?php echo $i; ?>"><?php echo $vrstica["logins"]; ?></td>
                <td class="tdLink<?php echo $i; ?>"><?php echo datum(2, $vrstica["last_login"]); ?></td>
                <td class="tdLink<?php echo $i; ?>">
                    <?php 
                    if($vrstica["fav_search"] == 1) echo "Google";
                    else if($vrstica["fav_search"] == 2) echo "Bing";
                    else if($vrstica["fav_search"] == 3) echo "Najdi.si";
                    else echo "None"; 
                    ?>
                </td>
            </tr>
		<?php
	}
	else {
		echo "Ni podatkov!";
	}
}

function izbrisiUser() {
    global $config;
    global $mydb;
    global $mydb1;
    $user_id = clean($_GET['izbrisiUser']);
	if($user_id != 1) {
		$delete = "DELETE FROM users WHERE id =  " . $user_id;
		$mydb->query($delete);
		if($mydb->result()) {
			$delete1 = "DELETE FROM links WHERE user_id = " . $user_id;
            $mydb1->query($delete1);
            if($mydb1->result()) {
                echo info("User was successfully deleted along with his links!");
                redirect("index.php?page=manageUsers");
            } else {
                echo error("Error with deleting links from selected user!");
            }
		} else {
			echo error("Error with deleting selected user!");
		}
	} else {
	   echo error("Cannot delete root!");
	}
}

function editUserAdmin($userId) {
	global $config;
	global $mydb;
	
	if($_POST['send']){
		$name = clean($_POST['name']);
		$surname = clean($_POST['surname']);
		$password = clean($_POST['password']);
		$confirm = clean($_POST['password2']);
		$email = clean($_POST['mail']);
		$gender = clean($_POST['gender']);
        $role = clean($_POST['role']);
		
		$errors = array();
        $errMesssage = "";
		
			if(!$name){
				$errors[] = "Name is not defined!";
                $errMesssage = $errMesssage . "Name is not defined!<br />";
			}
						
			if(!$surname){
				$errors[] = "Surnameame is not defined!";
                $errMesssage = $errMesssage . "Surnameame is not defined!<br />";
			}
			
			if(!$email){
				$errors[] = "E-mail is not defined!";
                $errMesssage = $errMesssage . "E-mail is not defined!<br />";
			}
			
			if(!$gender){
				$errors[] = "Gender is not defined!";
                $errMesssage = $errMesssage . "Gender is not defined!<br />";
			}
			
			if($name){
				if(!ctype_alnum($name)){
					$errors[] = "Name can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Name can only contain numbers and letters!<br />";
				}
			}
			
			if($surname){
				if(!ctype_alnum($surname)){
					$errors[] = "Surname can only contain numbers and letters!";
                    $errMesssage = $errMesssage . "Surname can only contain numbers and letters!<br />";
				}
			}
			
			if($email){
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				if(!preg_match($checkemail, $email)){
					$errors[] = "E-mail is not valid, must be name@server.tld!";
                    $errMesssage = $errMesssage . "E-mail is not valid, must be name@server.tld!<br />";
				}
			}
			
			if($email){
				$sql = "SELECT * FROM users WHERE mail='" . $email . "'";
				$mydb->query($sql);
				$userInfo = $mydb->row();
                    if($userId != $userInfo["id"]) {
    					if($mydb->recno() > 0){
    						$errors[] = "The e-mail address you supplied is already in use of another user!";
                            $errMesssage = $errMesssage . "The e-mail address you supplied is already in use of another user!<br />";
    					}
                    }
			}
			
			if(count($errors) > 0){
                echo error($errMesssage);
				echo "<br /><a href='?page=manageUsers'>Back</a>";
			}else {
			     if($password)
				    $sql = "UPDATE users SET name = '" . $name . "', surname = '" . $surname . "', mail = '" . $email . "', password = '" . md5($password) . "', role = '" . $role . "', gender = '" . $gender . "' WHERE id = " . $userId;
				else
				    $sql = "UPDATE users SET name = '" . $name . "', surname = '" . $surname . "', mail = '" . $email . "', role = '" . $role . "', gender = '" . $gender . "' WHERE id = " . $userId;
                $mydb->query($sql);
				if($mydb->result())
					echo info("You have successfully edited an account!") . "<br /><a href='?page=manageUsers'>Back</a>";
                else 
                    echo error("There was an error regarding the database! Please try again later!");                
			}
	}
    else {
	   $select = "SELECT * FROM users WHERE id = " . $userId;
       $mydb->query($select);
       if($mydb->recno()) $vrstica = $mydb->row();
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableRegister">
			<legend>Enter the following information</legend>
	 
			<table>
				<tr>
					<td>Name:
					</td>
					<td><input type="text" name="name" size="20" class="field" value="<?php echo $vrstica["name"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Surname:
					</td>
					<td><input type="text" name="surname" size="20" class="field" value="<?php echo $vrstica["surname"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Password:
					</td>
					<td><input type="password" name="password" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>Repeat password:
					</td>
					<td><input type="password" name="password2" size="20" class="field" />
					</td>
				</tr>
				<tr>
					<td>e-mail:
					</td>
					<td><input type="text" name="mail" size="25" class="field" value="<?php echo $vrstica["mail"] ?>" /> 
					</td>
				</tr>
				<tr>
					<td>Gender:
					</td>
					<td>
						<select name="gender">
							<option value="1" <?php if($vrstica["gender"] == 1) echo "selected";  ?>><b>Male</b></option>
							<option value="2" <?php if($vrstica["gender"] == 2) echo "selected";  ?>><b>Female</b></option>
						</select>
					</td>
				</tr>
                <tr>
					<td>Role:
					</td>
					<td>
						<select name="role">
							<option value="1" <?php if($vrstica["role"] == 1) echo "selected";  ?>><b>User</b></option>
							<option value="2" <?php if($vrstica["role"] == 2) echo "selected";  ?>><b>Admin</b></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
                        <input type="submit" name="send" value="Confirm" class="button" />&nbsp;
					    <input type="reset" name="reset" value="Reset" class="button" />&nbsp; &nbsp; &lowast;All fields are required&nbsp; &nbsp;
                        &nbsp; &nbsp;<input type="button" onclick="menu('tableRegister')" value="Close" class="button" />
					</td>
				</tr>
			</table>
			</fieldset>
			</form>
		<?php
	}
}
?>