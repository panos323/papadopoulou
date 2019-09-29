<?php

	if(!defined('Iznogoud')) {
	    header('HTTP/1.0 404 Not Found');
	    echo "<h1>404 Not Found</h1>";
	    echo "The page that you have requested could not be found.";
	    exit();
	}

	function dbConnect() {
		$GLOBALS['mysqli'] = '';
		$GLOBALS['mysqliError'] = "";
		$GLOBALS['mysqli'] = new mysqli(mysql_host, mysql_user, mysql_pass, mysql_db);
		if ($GLOBALS['mysqli']->connect_errno) {
			$GLOBALS['mysqliError'] = "Failed connecting to MySQL mysql://".mysql_host."/<br />".$GLOBALS['mysqli']->connect_error;
			return false;
		}else{
			$GLOBALS['mysqli']->is_connected = true;
			$GLOBALS['mysqli']->query("SET NAMES utf8");
			$GLOBALS['mysqli']->query("SET time_zone = 'Europe/Athens'");
			return true;
		}
	}

	function dbQuery($query) {
		$GLOBALS['mysqliError'] = "";
		if ( !$GLOBALS['mysqliRes'] = $GLOBALS['mysqli']->query( $query ) ) {
			$GLOBALS['mysqliError'] = $GLOBALS['mysqli']->error;
			return false;
		}else{
			return true;
		}
	}

	function dbMultiQuery($query) {
		$GLOBALS['mysqliError'] = "";
		if ( !$GLOBALS['mysqliRes'] = $GLOBALS['mysqli']->multi_query( $query ) ) {
			$GLOBALS['mysqliError'] = $GLOBALS['mysqli']->error;
			return false;
		}else{
			return true;
		}
	}
	
	function dbClose() {
		if( $GLOBALS['mysqli'] ) @$GLOBALS['mysqli']->close();
	}

	function dbEscape($text) {
		return $GLOBALS['mysqli']->real_escape_string($text);
	}

	function dbGetNumRows() {
		return $GLOBALS['mysqliRes']->num_rows;
	}

	function dbFetchArray() {
		return $GLOBALS['mysqliRes']->fetch_array();
	}

	function dbFetchAssoc() {
		return $GLOBALS['mysqliRes']->fetch_assoc();
	}

	function dbGetAffected() {
		return $GLOBALS['mysqli']->affected_rows;
	}

	function dbGetInsertId() {
		return $GLOBALS['mysqli']->insert_id;
	}

	function dbGetErrorMsg() {
		return $GLOBALS['mysqliError'];
	}
?>