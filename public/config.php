<?php

	if(!defined('Iznogoud')) {
	    header('HTTP/1.0 404 Not Found');
	    echo "<h1>404 Not Found</h1>";
	    echo "The page that you have requested could not be found.";
	    exit();
	}
	
define( "DEBUG", true );
//define( "DEBUG", false );

//Facebook
define( "fb_initApi", "true" );
define( "fb_appId", "2171285102918563" );
define( "fb_pageId", "" );
define( "fb_appSecret", "" );
//define( "fb_isCanvas", 1); //0 or 1




define( "googleAnalyticsId", "UA-140324376-1");
define( "facebookPixelId", "");
define( "googleRemarketing", "");

//if (fb_isCanvas) define( "fb_namespace", ''); //NO SLASHES eg. "lamda_secret_garage"

define("cacheVersion", "0");

/* 
 * no 
 * need 
 * to edit
 * the ones below 
 * in most of the cases 
 * */

//Server Environment
define( "serverUrl", "http://www.localhost/papadopoulou/public/");
define( "serverDir", dirname((__FILE__))."/");


$temp = explode("/", serverDir);
define("app_name", $temp[count($temp)-2]);
//define("appDir", $temp[count($temp)-4]."/".$temp[count($temp)-3]."/".$temp[count($temp)-2]."/");

define( "controllerDir", serverDir."controllers/" );
define( "viewDir", serverDir."views/" );
define( "imageDir", "images/" );

//Files
define( "terms", serverUrl."terms.pdf" );
define( "shareImage", serverUrl."images/share.jpg" );
define( "shareUrl", "" );


//define( "shareUrl", "https://apps.facebook.com/".fb_namespace."/" );

//MySQL
define( "mysql_host", "localhost" );
define( "mysql_user", "root" );
define( "mysql_pass", "" );
define( "mysql_db", "papadopoulou" );
define( "mysql_table_prefix", 'nt' );
define( "mysql_table_users", "users" );
define( "mysql_table_shares", mysql_table_prefix."_shares" );
define( "mysql_table_games", mysql_table_prefix."_games" );
define( "mysql_table_invites", mysql_table_prefix."_invites" );

?>