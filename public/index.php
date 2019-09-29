<?php

define('Iznogoud', true);

include('config.php');
include('functions/functions.php');
include('functions/mysql.php');

$GLOBALS['page'] = 'main';
init();

//if the installation file has not been removed, use it!
if (file_exists('controllers/db_installation.php')) {
	include(controllerDir.'db_installation.php');
}else{

	//determine which page we need to load
	if( isAjaxRequest() ){
		$GLOBALS['page'] = 'ajax';
	}else{
		$_SESSION['Iznogoud']=true;
	}
	
	
	//always include app-wide controller
	include( controllerDir.'app.php' );
	
	
	//if the controller exists, load it. Otherwise, load the view
	if ( file_exists( controllerDir.$GLOBALS['page'].'.php' ) ) {
		include( controllerDir.$GLOBALS['page'].'.php' );
	}else if ( file_exists( viewDir.$GLOBALS['page'].'.php' ) ) {
		include( viewDir.$GLOBALS['page'].'.php' );
	}	
	
}
dbClose();

?>
