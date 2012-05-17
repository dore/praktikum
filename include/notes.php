<?php
function addNote() {
    global $config;
	global $mydb;
	
	if($_POST['send']){
		$title = clean($_POST['title']);
		$text = clean($_POST['elm1']);

		$errors = array();
        $errMesssage = "";
        
        if(!$title){
			$errors[] = "Title is not defined!";
            $errMesssage = $errMesssage . "Title is not defined!<br />";
		}						
		if(!$text){
			$errors[] = "Text is not defined!";
            $errMesssage = $errMesssage . "Text is not defined!<br />";
		}
        
		if(count($errors) > 0){
            echo error($errMesssage);
			echo "<br /><a href='?page=addNote'>Back</a>";
		}else {
            $date = date("Y") . "-" . date("m") . "-" . date("d");
			$sql = "INSERT INTO notes (title, text, submitter_id, user_id, date, status) VALUES ('" . $title . "','" . $text . "', '" . $_SESSION["portal_id"] . "', '" . $_SESSION["portal_id"] . "', '" . $date . "', 1)";
			$mydb->query($sql);
			if($mydb->result())
				echo info("You have successfully added <b>" . $title . "</b> to your notes!") . "<br /><a href='?page=allNotes'>Back</a>";
            else echo error("There was an error!");
		}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableAddNote">
			<legend>Enter the following information</legend>
	 
			<table>
				<tr>
					<td>Title:
					</td>
					<td><input type="text" name="title" class="fieldTextTitle" />
					</td>
				</tr>
                <tr>
					<td colspan="2">
                        <textarea id="elm1" name="elm1" style="width: 589px; height: 500px;">
                            
                        </textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="send" value="Confirm" class="button" />
					&nbsp; &nbsp; &lowast;All fields are required
					</td>
				</tr>
			</table>
			</fieldset>
			</form>
            <p class="pSubtitles">*Resize text field as you wish at bottom right corner</p>
		<?php
	}
} 

function allNotes($user_id) {
	global $config;
	global $mydb;
	
	$select = "SELECT * FROM notes WHERE user_id = " . $user_id . " ORDER BY date DESC";
	$mydb->query($select);
	
	$i = 1;
    $count = 1;
	if($mydb->recno() == 0) {
		?>
		<tr>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>">You have no notes yet!</td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
            <td class="tdLink<?php echo $i ?>"></td>
        </tr>
        <?php
	} else {
        while($vrstica = $mydb->row()) {
            enNote($vrstica["id"], $i, $count, $vrstica["submitter_id"], $vrstica["status"]);
            if($i == 1) $i = 2;
            else $i = 1;
            $count++;
		}
	}
}

function enNote($id, $i, $count, $senderId, $status) {
	global $config;
	global $mydb1;
    
    $select = "SELECT * FROM users WHERE id = " . $senderId;
	$mydb1->query($select);
	if($mydb1->recno() == 1) {
	   $vrstica = $mydb1->row();
	   $sender = $vrstica["name"] . " " . $vrstica["surname"];
	}
	$select = "SELECT * FROM notes WHERE id = " . $id;
	$mydb1->query($select);
	
	if($mydb1->recno() == 1) {
		$vrstica = $mydb1->row();
		?>
        <tr class="tdLink<?php echo $i ?>" id="<?php echo $count ?>">
            <td><?php echo $count ?>.</td>
            <td><?php echo $vrstica["title"]; ?></td>
            <td><span class="pLinkDomain"><?php echo strip_tags(skrci($vrstica["text"])); ?></span></td>
            <td>
                <?php
                if($status == 2) {
                    echo "<span class='pLinkSender'><b>" . $sender . "</b></span>";
                } else {
                    echo "<span class='pLinkUrl'>You</span>";
                }
                ?>
            </td>
            <td><a href="?page=allNotes&izbrisiNote=<?php echo $vrstica["id"] ?>"  onclick="return makesure();"><img src="images/delete.png" alt="up" title="Delete" /></a><a href="?page=allNotes&viewNote=<?php echo $vrstica["id"]; ?>"><img src="images/edit.png" border="0" alt="Edit" title="Edit" /></a></td>
        </tr>
		<?php
	}else {
		echo "Ne obstaja";
	}
}

function izbrisiNote($user_id) {
    global $config;
    global $mydb;
    $noteId = clean($_GET['izbrisiNote']);
    $select = "SELECT user_id FROM notes WHERE id =  " . $noteId;
    $mydb->query($select);
    if($mydb->recno() == 1)
		$vrstica = $mydb->row();
    if($user_id == $vrstica["user_id"]) {
    	$delete = "DELETE FROM notes WHERE id =  " . $noteId;
    	$mydb->query($delete);
    	if($mydb->result()) {
            echo info("<br />Selected note was successfully deleted!") . "<a href='?page=allNotes'>Continue</a>";
            redirect("?page=allNotes");
        } else {
    		echo error("There was an error regarding the database! Please try again later!");
            redirect("?page=allNotes");
        }
    }
    else {
        echo error("You do not have privilages to delete the chosen note!");
    }
}

function sendNote() {
    global $config;
	global $mydb;
 
	if($_POST['send']){
		$title = clean($_POST['title']);
		$text = clean($_POST['elm1']);
        $email = clean($_POST['reciever']);

		$errors = array();
        $errMesssage = "";
        
        if(!$title){
			$errors[] = "Title is not defined!";
            $errMesssage = $errMesssage . "Title is not defined!<br />";
		}						
		if(!$text){
			$errors[] = "Text is not defined!";
            $errMesssage = $errMesssage . "Text is not defined!<br />";
		}
        if(!$email){
			$errors[] = "Recipient is not defined!";
            $errMesssage = $errMesssage . "Recipient is not defined!<br />";
		}
        if($email){
				$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				if(!preg_match($checkemail, $email)){
					$errors[] = "E-mail is not valid, must be name@server.tld!";
                    $errMesssage = $errMesssage . "E-mail is not valid, must be name@server.tld!<br />";
				}
		}
        
		if(count($errors) > 0){
            echo error($errMesssage);
			echo "<br /><a href='?page=addNote'>Back</a>";
		}else {
            $sql = "SELECT id FROM users WHERE mail = '" . $email . "'";
            $mydb->query($sql);
            if($mydb->recno() == 1)
                $recId = $mydb->row();
            else 
                echo error("There was an error!") . "<br /><a href='?page=sendNote'>Back</a>";
                
            $date = date("Y") . "-" . date("m") . "-" . date("d");
			$sql = "INSERT INTO notes (title, text, submitter_id, user_id, date, status) VALUES ('" . $title . "','" . $text . "', '" . $_SESSION["portal_id"] . "', '" . $recId["id"] . "', '" . $date . "', 2)";
			$mydb->query($sql);
			if($mydb->result())
				echo info("You have successfully sent <b>" . $title . "</b> to your notes!") . "<br /><a href='?page=allNotes'>Back</a>";
            else {
                echo error("There was an error!");
                echo $recId . "<br />";
                echo $text . "<br />";
                echo $title . "<br />";
                echo $email . "<br />";
            }
		}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableAddNote">
			<legend>Enter the following information</legend>
	 
			<table>
				<tr>
					<td>Title:
					</td>
					<td><input type="text" name="title" class="fieldTextTitle" />
					</td>
				</tr>
                <tr>
					<td valign="top">To:
					</td>
					<td><input type="text" name="reciever" class="field" id ="inputString" onkeyup="showHint(this.value);" />
                    <span id="txtHint"></span>
                    <p class="pSubtitles">*Start typing an email address of a registered user.</p>
					</td>
				</tr>
                <tr>
					<td colspan="2">
                        <textarea id="elm1" name="elm1" style="width: 589px;">
                            
                        </textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="send" value="Confirm" class="button" />
					&nbsp; &nbsp; &lowast;All fields are required
					</td>
				</tr>
			</table>
			</fieldset>
			</form>
            <p class="pSubtitles">*Resize text field as you wish at bottom right corner</p>
		<?php
	}
} 

function viewNote($userId) {
	global $config;
	global $mydb;
	$noteId = clean($_GET["viewNote"]);
    
	if($_POST['send']){
		$title = clean($_POST['title']);
		$text = clean($_POST['elm1']);
		
		$errors = array();
        $errMesssage = "";
		
			if(!$title){
				$errors[] = "Title is not defined!";
                $errMesssage = $errMesssage . "Title is not defined!<br />";
			}
						
			if(!$text){
				$errors[] = "Text is not defined!";
                $errMesssage = $errMesssage . "Text is not defined!<br />";
			}
			
			if(count($errors) > 0){
                echo error($errMesssage);
				echo "<br /><a href='?page=allNotes'>Back</a>";
			}else {
				$sql = "UPDATE notes SET title = '" . $title . "', text = '" . $text . "' WHERE id = " . $noteId;
                $mydb->query($sql);
				if($mydb->result())
					history(-2);
                else 
                    echo error("There was an error regarding the database! Please try again later!");                
			}
	}
    else {
	   $select = "SELECT * FROM notes WHERE user_id = " . $userId . " AND id = " . $noteId;
       $mydb->query($select);
       if($mydb->recno()) $vrstica = $mydb->row();
       else echo error("You have no privilages to edit this note!");
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<fieldset id="tableAddNote">
			<legend>Enter the following information</legend>
			<table>
				<tr>
					<td>Title:
					</td>
					<td align="left"><input type="text" name="title" class="fieldTextTitle" value="<?php echo $vrstica["title"]; ?>" /></td>
				</tr>
                <tr>
					<td colspan="2">
                        <textarea id="elm1" name="elm1">
                            <?php echo $vrstica["text"]; ?>
                        </textarea>
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
                        &nbsp; &nbsp;<input type="button" onclick="menu('tableAddNote')" value="Close" class="button" />
					</td>
				</tr>
			</table>
			</fieldset>
		</form>
		<?php
	}
}
?>