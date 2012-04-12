<?php
function register_normal() {
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
				$sql = "INSERT INTO users (name, surname, mail, password, role, gender, logins, last_login, fav_search, active) VALUES ('" . $name . "','" . $surname . "','" . $email . "','" . md5($password) . "', 1,'" . $gender . "', 0, 0000-00-00, 0, 1)";
				$mydb->query($sql);
				if($mydb->result())
					echo info("You have successfully registered as <b>" . $name . " " . $surname . "</b>! <br /><a href='index.php'>Back</a>");
                else 
                    echo error("Error in database!") . "<br /> <a href ='?page=register'>Back</a>";
                        
			}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
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
						<select name="gender" class="field">
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
					<td colspan="2"><input type="submit" name="send" value="Register" class="button" />&nbsp;
					<input type="reset" name="reset" value="Reset" class="button" />&nbsp; &nbsp; &lowast;All fields are required
					</td>
				</tr>
			</table>
			</form>
		<?php
	}
}
?>