<?php

/**
 * Database connection object. Used to simplify database conectivity in web application. Currently
 * only supports mySql connections. Connection to the database is set up during construction of the
 * object.
 *
 * @author		Andrej
 * @version		1.0
 * @date		2004-05-24
 */
class Db {
	var $cconnection;
	var $cok;
	var $cquery;
	var $cresult;
	var $crecno;

	/**
	 * Create a new database connection object using the specified parameters. Connection to the 
	 * database is setup at this point.
	 *
	 * @param	$pdb	Database name.
	 * @param	$phost	Hostname of the database server.
	 * @param	$puser	Username for connecting to the database.
	 * @param	$ppass	Password for connecting to the database.
	 */	
	function Db($pdb = "", $phost = "", $puser = "", $ppass = "") {
		$this->cconnection = false;
		$this->cok = false;
		$this->cquery = "";
		$this->cresult = false;
		$this->crecno = 0;
		
		if ((strlen($ppass) == 0) && isset($GLOBALS["GDBPass"])) $ppass = $GLOBALS["GDBPass"];
		if ((strlen($puser) == 0) && isset($GLOBALS["GDBUser"])) $puser = $GLOBALS["GDBUser"];
		if ((strlen($phost) == 0) && isset($GLOBALS["GDBHost"])) $phost = $GLOBALS["GDBHost"];
		if ((strlen($pdb) == 0) && isset($GLOBALS["GDBase"])) $pdb = $GLOBALS["GDBase"];

		if ((strlen($phost) == 0) && (strlen($puser) == 0) && (strlen($ppass) == 0)) {
			$this->cconnection = @mysql_connect();
		} else if ((strlen($puser) == 0) && (strlen($ppass) == 0)) {
			$this->cconnection = @mysql_connect($phost);
		} else if (strlen($ppass) == 0) {
			$this->cconnection = @mysql_connect($phost, $puser);
		} else {
			$this->cconnection = @mysql_connect($phost, $puser, $ppass);
		} //if ((strlen($phost
		
		if (($this->cconnection != false) && (strlen($pdb) > 0)) {
			$lselected = @mysql_select_db($pdb, $this->cconnection);
			if ($lselected != false) $this->cok = true;
			else $this->cok = false;
		} else {
			$this->cok = false;
		} //if (($this->connection
	} //function Db
	
	/**
	 * Send a query to the database.
	 *
	 * @param	$pquery	SQL query.
	 */
	function query($pquery) {
		if ($this->cok) {
			$this->cquery = $pquery;
			$this->cresult = @mysql_query($this->cquery, $this->cconnection);

			if ($this->cresult != false) {
				$ltype = strtoupper(substr($this->cquery, 0, 6));
				if ($ltype == "SELECT") {
					$this->crecno = @mysql_num_rows($this->cresult);
				} else if (($ltype == "INSERT") || ($ltype == "UPDATE") || ($ltype == "DELETE")) {
					$this->crecno = @mysql_affected_rows($this->cconnection);
				} //if ($ltype
			} //if ($this->cresult
		} //if ($this->cok
	} //function query
	
	/**
	 * Return number of rows in the result for a SELECT query or  number of affected rows for
	 * INSERT, UPDATE and DELETE queries.
	 *
	 * @return	Number of selected/affected rows.
	 */
	function recno() {
		return $this->crecno;
	} //function recno
	
	/**
	 * Returns the result of the query. This are rows for a SELECT query and TRUE for DELETE, 
	 * INSERT, UPDATE. If the query was unsuccessful, return FALSE.
	 *
	 * @return	Query result.
	 */
	function result() {
		return $this->cresult;
	} //function result
	
	/**
	 * Return current row from the query result. As the row is returned, current row moves one
	 * record forward. When there are no more rows FALSE is returned and row pointer is 
	 * repositioned to the begining of the result.
	 *
	 * @return	Current row (an associative array).
	 */
	function row() {
		if ($this->cok && ($this->cresult != false) && ($this->crecno > 0)) {
			$lrow = @mysql_fetch_array($this->cresult);
			if ($lrow != false) {
				return $lrow;
			} else {
				@mysql_data_seek($this->cresult, 0);
				return false;
			} //if ($lrow
		} else {
			return false;
		} //if ($this->cok
	} //function row
} //class Db

?>