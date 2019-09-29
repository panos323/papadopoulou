<?php

if(!defined('Iznogoud') ) {
    header('HTTP/1.0 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}

if( DEBUG ){
	error_reporting(E_ALL);
}else{
	error_reporting(0);
}

function isAjaxRequest(){
	if( isset($_POST) && isset($_POST['page']) && $_POST['page']=='ajax' ) return true;
	return false;
}

/*function isOnFacebook(){
	return isset($GLOBALS['data']['signedRequest']) && $GLOBALS['data']['signedRequest'];
}

function isFacebookPage(){
	return isset($GLOBALS['data']['fbPageId']) && $GLOBALS['data']['fbPageId'];
}

function redirectToCanvas(){
	print "<script>window.top.location.href = 'https://apps.facebook.com/". fb_namespace . "/';</script>";
	die();
}

function redirectToTab(){
	print "<script>window.top.location.href = 'https://www.facebook.com/pages/".fb_pageId."/".fb_pageId."?sk=app_".fb_appId."';</script>";
	die();
}*/

function init(){
	dbConnect();
	//if ( !isset( $_SESSION ) ) session_start();
	//header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
}


//MySQL
/*
function db_storeUserData(){
	if( isset( $GLOBALS['data'] ) ) {
		$query ="INSERT IGNORE INTO `".mysql_table_users."` SET 
					`fbUserId`='".$GLOBALS['data']['fbUserId']."', 
					`fbName`='".dbEscape( $GLOBALS['data']['fbName'] )."', 
					`fbSurname`='".dbEscape( $GLOBALS['data']['fbSurname'] )."', 
					`fbEmail`='".$GLOBALS['data']['fbEmail']."', 
					`fbGender`='".$GLOBALS['data']['fbGender']."',  
					`fbAgeMin`='".$GLOBALS['data']['fbAgeMin']."', 
					`fbAgeMax`='".$GLOBALS['data']['fbAgeMax']."'";
		if( $GLOBALS['mysqli']->query( $query ) ) return true;
	}
	return false;
}

function isUserDataStored() {
	$query = "SELECT `fbUserId` FROM `".mysql_table_users."` WHERE `fbUserId` = '".$GLOBALS['data']['fbUserId']."'";
	if( dbQuery( $query ) ){
		if( dbGetNumRows() > 0 ){
			return true;
		}
	}
	return false;
}*/



//Facebook
/*	
function fb_initApi(){
		require 'fbapi/facebook.php';
		$GLOBALS['facebook'] = new Facebook(array(
			'appId'  => fb_appId,
			'secret' => fb_appSecret,
			'cookie' => true
		));
}

function fb_LoadUserData(){
	$GLOBALS['hasAuthorised'] = 0;
	if( isset($GLOBALS['facebook']) && $GLOBALS['facebook'] ){
		try {
			$user = $GLOBALS['facebook']->api('me?fields=first_name,last_name,email,gender,birthday,age_range');
			$GLOBALS['hasAuthorised'] = 1;
			$GLOBALS['data']['fbUserId'] = $user["id"];
			$GLOBALS['data']['fbName'] = $user['first_name'];
			$GLOBALS['data']['fbSurname'] = $user['last_name'];
			$GLOBALS['data']['fbEmail'] = isset($user['email']) ? $user['email'] : "";
			$GLOBALS['data']['fbGender'] = isset($user['gender']) ? $user['gender'] : "";
			$GLOBALS['data']['fbAgeMin'] = 0;
			if( isset($user['age_range']['min']) ) $GLOBALS['data']['fbAgeMin'] = $user['age_range']['min'];
			$GLOBALS['data']['fbAgeMax'] = 999;
			if( isset($user['age_range']['max']) ) $GLOBALS['data']['fbAgeMax'] = $user['age_range']['max'];
			return true;
		} catch (FacebookApiException $e) {
			//print 'fb_LoadUserData Catch: '.$e.'<br />';
		}
	}
	return false;
}

function fb_hasAuthorised(){
	return isset($GLOBALS['hasAuthorised']) && $GLOBALS['hasAuthorised'] ? true : false;
}


function fb_printLoginUrl(){
	if (fb_isCanvas) {
        if ( isMobile() ){
            return $GLOBALS['facebook']->getLoginUrl(array('scope' => 'email, public_profile'));
        }else{
        return $GLOBALS['facebook']->getLoginUrl(array('scope' => 'email, public_profile',
            'redirect_uri' => 'https://apps.facebook.com/' . fb_namespace ."?skipWelcome=true" ));
    }
}
	else{
		return $GLOBALS['facebook']->getLoginUrl(array('scope' => 'email',
										'redirect_uri' => 'https://www.facebook.com/pages/'.fb_pageId.'/'.fb_pageId.'?sk=app_'.fb_appId.'&app_data=skipWelcome'));
	}
}*/

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function isIphone($user_agent=NULL) {
    if(!isset($user_agent)) {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    return (strpos($user_agent, 'iPhone') !== FALSE);
}

//Notice: use to detect the page Id, in case you don't already know it
/*
function fb_loadSignedRequest(){
	if( isset($GLOBALS['facebook']) && $GLOBALS['facebook'] ){
		$signed_request = $GLOBALS['facebook']->getSignedRequest();
		if( isset( $signed_request["page"] ) ){
			$GLOBALS['data']['fbPageId'] = $signed_request["page"]["id"];
			if( isset( $signed_request["user"] ) ){
				$GLOBALS['data']['hasLiked'] = $signed_request["page"]["liked"];
			}
		}
		if( isset( $signed_request["app_data"] ) ){
			$GLOBALS['app_data'] = $signed_request["app_data"];
		}
	}
}

function fb_postPhotoToFacebook($photo, $message) {
	try {
		$ret_obj = $GLOBALS ['facebook']->api ( '/me/photos', 'POST', array (
				'source' => '@' . $photo,
				'message' => $message 
		) );
		db_storePhotoData ( $ret_obj ['id'] );
		return true;
	} catch ( FacebookApiException $e ) {
		$login_url = $GLOBALS ['facebook']->getLoginUrl ( array (
				'scope' => 'photo_upload' 
		) );
		echo '<script>window.location = "'.$login_url. '";</script>';
	}
}

function fb_parseSignedRequest($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = fb_base64_url_decode($encoded_sig);
  $data = json_decode(fb_base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // Adding the verification of the signed_request below
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function fb_base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}*/

function timesPlayed() {
    if (!isset($GLOBALS['data']['fbUserId'])){
        return 0;
    }
    $number = 0;
    $query = "SELECT id FROM `".mysql_table_games."` WHERE fbUserId='".$GLOBALS['data']['fbUserId']."'";
    if (dbQuery($query)) {
        $number = dbGetNumRows();
    }
    return intval($number);
}

function timesPlayedToday() {
    if (!isset($GLOBALS['data']['fbUserId'])){
        return 0;
    }
    $number = 0;
    $query = "SELECT id FROM `".mysql_table_games."` WHERE fbUserId='".$GLOBALS['data']['fbUserId']."' AND created BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'";
    if (dbQuery($query)) {
        $number = dbGetNumRows();
    }
    return intval($number);
}

function haveShareToday(){
    if (!isset($GLOBALS['data']['fbUserId'])){
        return 0;
    }
    $number = 0;
    $query = "SELECT id FROM `".mysql_table_shares."` WHERE fbUserId='".$GLOBALS['data']['fbUserId']."' AND created BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'";
    if (dbQuery($query)) {
        $number = dbGetNumRows();
    }
    return intval($number);
}

function httpGetRequest($url){
    $curl_handle = curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$url);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_HEADER, 0);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, false);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    return json_decode($buffer,true);
}

function httpPostRequest($url){
    $curl_handle = curl_init();
    $curlConfig = array(
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    );
    //curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    //curl_setopt($curl_handle,CURLOPT_HEADER, 0);
    //curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($curl_handle, $curlConfig);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    return json_decode($buffer,true);
}
/*
function sendNotification($text,$idsToSend){
    if( !empty($text) && !empty($idsToSend) ){
        foreach($idsToSend as $id){
            $notificationReturn = httpPostRequest('https://graph.facebook.com/'.$id.'/notifications?access_token='.fb_appToken.'&template="'.$text);
            return $notificationReturn;
        }
    }
}*/

?>