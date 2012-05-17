<?php
define('FACEBOOK_APP_ID', '264106710283984');
define('FACEBOOK_SECRET', '3bf6f6ba0648397ebdfc97fbe82401c1');

function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

if ($_REQUEST) {
    $response = parse_signed_request($_REQUEST['signed_request'], FACEBOOK_SECRET);
  	global $config;
	global $mydb;
	$name = clean($response[registration][first_name]);
	$surname = clean($response[registration][last_name]);
	$password = clean($response[registration][password]);
	$email = clean($response[registration][email]);
	$gender = clean($response[registration][gender]) == "male" ? 1 : 2;
	
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
} else {
  echo '$_REQUEST is empty';
}
?>