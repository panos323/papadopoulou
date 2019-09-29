<?php
//If the app has a game to show, uncomment the following
//this is for tab applications 
/*
$GLOBALS['game2show'] = 0;

if ( isset( $GLOBALS['app_data'] ) &&  strstr( $GLOBALS['app_data'], 'skipWelcome' ) !== true && is_numeric($GLOBALS['app_data'])){
    $GLOBALS['game2show'] = intval($GLOBALS['app_data']);
}

if ($GLOBALS['game2show']){
    $query = "SELECT a.*, b.fbName, b.fbSurname FROM `".mysql_table_games."` a LEFT JOIN `".mysql_table_users."` b ON a.fbUserId = b.fbUserId WHERE a.id=".$GLOBALS['game2show'];
    if(dbQuery($query))
    {
        $row = dbFetchAssoc();
        $GLOBALS['game2showData']['fbUserId'] = $row['fbUserId'];
        $GLOBALS['game2showData']['fbName'] = $row['fbName']. " ". $row['fbSurname'];
    }
}
*/

// and this is for canvas applications
// (used in conjuction with .htaccess redirect)
/*
 if ( isset($_GET['game']) && $_GET['game']){
    $GLOBALS['game2show'] = intval($_GET['game']);
}
*/