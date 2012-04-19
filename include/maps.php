<?php
function addMap() {
    global $config;
	global $mydb;
	
	if($_POST['send']){
		$title = clean($_POST['title']);

		$errors = array();
        $errMesssage = "";
        
        if(!$title){
			$errors[] = "Title is not defined!";
            $errMesssage = $errMesssage . "Folder name is not defined!<br />";
		}						
        
		if(count($errors) > 0){
            echo error($errMesssage);
			echo "<br /><a href='?page=addMap'>Back</a>";
		}else {
			$sql = "INSERT INTO maps (name, user_id) VALUES ('" . $title . "', '" . clean($_SESSION["portal_id"]) . "')";
			$mydb->query($sql);
			if($mydb->result()) {
				echo info("You have successfully added <b>" . $title . "</b> as your folder!") . "<br /><a href='?page=allMaps'>Back</a>";
                redirect("index.php?page=allMaps");
            } else echo error("There was an error regarding the database. Please try later!");
		}
	}else {
		?>
		<form method="post" action="<?php $PHP_SELF; ?>">
			<table>
				<tr>
					<td valign="top">Folder name:
					</td>
					<td><input type="text" name="title" class="field" style="width: 160px" />
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="divPotrdiCrta"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="send" value="Ok" class="button addMapSubmit" style="width: 90px" />&nbsp;
						<input type="reset" name="cancle" value="Cancle" class="button addMapSubmit" style="width: 90px" /><br />
					</td>
				</tr>
			</table>
			</form>
		<?php
	}
}

function menuMaps() {
	global $config;
	global $mydb;
	
	$select = "SELECT * FROM maps WHERE user_id = " . $_SESSION["portal_id"];
	$mydb->query($select);
	
	if($mydb->recno() == 0) {
		?>
		<li>No folders</li>
        <?php
	} else {
        while($vrstica = $mydb->row()) {
            ?>
            <li onclick="document.location.href='index.php?page=viewMap&mapId=<?php echo $vrstica["id"]; ?>';"><?php echo $vrstica["name"]; ?></li>
            <?php
		}
	}
}

function allMaps() {
	global $config;
	global $mydb;
	
	$select = "SELECT * FROM maps WHERE user_id = " . $_SESSION["portal_id"];
	$mydb->query($select);
	
	$i = 1;
	if($mydb->recno() == 0) {
		?>
		<tr>
            <td class="tdLink<?php echo $i ?>">You have no folders! [<a href="#" id="addMapButton">add</a>]</td>
            <td class="tdLink<?php echo $i ?>"></td>
        </tr>
        <?php
	} else {
        while($vrstica = $mydb->row()) {
            enaMapa($vrstica["id"], $i);
            if($i == 1) $i = 2;
            else $i = 1;
		}
        ?>
        <tr class="tdLink<?php echo $i ?>" id="<?php echo $count ?>">
            <td><a href="index.php?page=allMaps&map=0" title="Open map">Unassigned</a></td>
            <td>O</td>
        </tr>
        <?php
	}
}

function enaMapa($id, $i) {
	global $config;
	global $mydb1;
    
	$select = "SELECT * FROM maps WHERE id = " . $id;
	$mydb1->query($select);
	
	if($mydb1->recno() == 1) {
		$vrstica = $mydb1->row();
		?>
        <tr class="tdLink<?php echo $i ?>" id="<?php echo $count ?>">
            <td><a href="index.php?page=allMaps&map=<?php echo $vrstica["id"]; ?>" title="Open folder"><?php echo skrci($vrstica["name"]); ?></a></td>
            <td><a href="?page=allMaps&deleteMap=<?php echo $vrstica["id"] ?>"  onclick="return makesure();"><img src="/images/delete.png" alt="up" title="Delete" /></a></td>
        </tr>
		<?php
	} else {
		echo "Ne obstaja";
	}
}

function deleteMap($user_id) {
    global $config;
    global $mydb;
    global $mydb1;
    $id = clean($_GET['deleteMap']);
	$delete = "DELETE FROM maps WHERE id =  " . $id ." AND user_id = " . $user_id;
	$mydb->query($delete);
	if($mydb->result()) {
		$delete1 = "DELETE FROM links WHERE map_id = " . $id;
        $mydb1->query($delete1);
        if($mydb1->result()) {
            echo info("Folder was successfully deleted along with its links!");
            redirect("/allMaps");
        } else {
            echo error("Error with deleting links from the selected folder!");
        }
	} else {
		echo error("Error with deleting selected folder!");
	}
}
?>