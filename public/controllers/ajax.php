<?php
	if(!defined('Iznogoud')) {
	    header('HTTP/1.0 404 Not Found');
	    echo "<h1>404 Not Found</h1>";
	    echo "The page that you have requested could not be found.";
	    exit();
	}
	session_start();

	$res = array( 'res' => 0 );
	
	switch( $_POST['action'] ){
		case 'storePostToFeed':
			if( isset( $_POST['fbUserId'] ) && isset( $_POST['post_id'] ) ){
				$GLOBALS['data']['fbUserId'] = dbEscape( $_POST['fbUserId'] );
				$GLOBALS['postId'] = dbEscape( $_POST['post_id'] );
				if( dbQuery("INSERT INTO `".mysql_table_shares."` SET `fbUserId` = '".$GLOBALS['data']['fbUserId']."', `postId` = '".$GLOBALS['postId']."'") ){
					$res['res'] = 1;
				}
			}
			print json_encode( $res );
			break;
			
		case 'storeInvites':
	        if( isset( $_POST['fbUserId'] ) && $_POST['fbUserId'] > 0 && isset( $_POST['post_id'] ) ){
	        	$GLOBALS['data']['fbUserId'] = dbEscape( $_POST['fbUserId'] );
	            $query = "INSERT INTO `".mysql_table_invites."` (fbUserId, invitationId, invitedFbUserId) VALUES ";
	            foreach ($_POST['shares'] as $share)
	            {
	                $share = dbEscape($share);
	                $query .= "('".dbEscape($_POST['fbUserId'])."', '".dbEscape($_POST['post_id'])."', '".$share."'),";
	            }
	            //remove the last comma
	            $query = substr($query,0,-1);
	
	            if( $result = dbQuery($query))
	            {
	                $res['res'] = 1;
	            }
	        }
	        print json_encode( $res );
			break;
			
		case 'storeShare':
			if( isset( $_POST['response'] )
			&& isset( $_POST['fbUserId']) )
			{
				$fbUserId = dbEscape($_POST['fbUserId']);
				$response= dbEscape($_POST['response']);
				
				$query = "INSERT INTO ".mysql_table_shares." (fbUserId, postid) VALUES (".$fbUserId.", '".$response."')";
				if(dbQuery($query))
				{
					$res['res'] = 1;
				}
				
			}
			
			print json_encode( $fbUserId);
			break;
			
			
		case 'checkLogFBUser':
        	if( isset( $_POST['email'] ) && !empty($_POST['email']) )
        	{
        		$email = dbEscape($_POST['email']);
				$query2 = "SELECT * FROM ".mysql_table_users." WHERE email = '".$email."'";
				$myObj = new stdClass();
				            
				if( dbQuery( $query2 ) ){
					if(	dbGetNumRows() < 1 ){
						$query = "INSERT INTO ".mysql_table_users." (email, fb_user) VALUES ('".$email."', 1)";
						dbQuery($query);
						$myObj->register = 1;
					}else{
						$myObj->register = 0;
					}
				}

				
	
        	}
        	print json_encode($myObj);
        	break;
				
		case 'storeUser':
			if( isset( $_POST['full_name'] ) && !empty($_POST['full_name'])
				&& isset( $_POST['email']) && strlen($_POST['email']) >= 6
				&& isset( $_POST['phone']) && strlen($_POST['phone']) >= 9 
				&& isset( $_POST['password']) && strlen($_POST['password']) >= 8 
				&& isset( $_POST['captcha']) && !empty($_POST['captcha'])
				)
        	{	

				$secret = "6Lcv-LoUAAAAAMvx23jVDRt1UjHETssQ0qGU3dmu"; 
				$response = $_POST["captcha"];
				$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
				$responseData = json_decode($verify);	

				if ($responseData->success) {
					$full_name = dbEscape($_POST['full_name']);
					$email = dbEscape($_POST['email']);
					$phone = dbEscape($_POST['phone']);				
					$password= password_hash(dbEscape($_POST['password']), PASSWORD_DEFAULT);
					$newsletter  = dbEscape($_POST['newsletter']);	

					$query2 = "SELECT * FROM ".mysql_table_users." WHERE email = '".$email."'";
								
					if( dbQuery( $query2 ) ){
						if(	dbGetNumRows() < 1 ){
							$query = "INSERT INTO ".mysql_table_users." (full_name, email, phone, password, newsletter, date) VALUES ('".$full_name."', '".$email."', '".$phone."', '".$password."', '".$newsletter."', NOW() )";
							dbQuery($query);
							echo "register_success";
							$_SESSION['registeredUser'] = true;
							$_SESSION['email'] = $email;
						}else{
							echo "register_email_exists";
						}
					}
				}
        	}
			break;
			
		case 'logUser':
			if( isset( $_POST['email'] ) && !empty($_POST['email'])
				&& isset( $_POST['password']) && strlen($_POST['password']) >= 8 
				&& isset( $_POST['captcha_login']) && !empty($_POST['captcha_login'])
				)
			{   

				$secret_login = "6Lcv-LoUAAAAAMvx23jVDRt1UjHETssQ0qGU3dmu"; 
				$response_login = $_POST["captcha_login"];
				$verify_login = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_login}&response={$response_login}");
				$responseData_login = json_decode($verify_login);	

				if ($responseData_login->success) {
					$email = dbEscape($_POST['email']);
					$password= dbEscape($_POST['password']);
					$query2 = "SELECT * FROM ".mysql_table_users." WHERE email = '".$email."' LIMIT 1 ";
								
					if( dbQuery( $query2 ) ){
						if(	dbGetNumRows() > 0 ){
							$row = dbFetchAssoc();
							$hash =  $row['password'];
							if (password_verify($password, $hash)) {
								echo "rightCrendetials";
								$_SESSION['loggedUser'] = true;
								$_SESSION['email'] = $email;
							} else {
								echo "wrongCrendetials";
							}
						
						} else {
							echo "wrongCrendetials2";
						}
					
					} else {
						echo "wrongCrendetials3";
					}
				}
			
			}
		break;

		case 'logOutUser':
			session_start();
			session_unset();
			session_destroy();
			
			header("location:index.php");
			exit();
		break;


		case 'submitGame':
        if(isset($_SESSION['email'])) {

			$email = dbEscape($_SESSION['email']);
			$query2 = "SELECT email FROM ".mysql_table_games." WHERE email = '".$email."' AND Date(created)=Curdate()";

			if( dbQuery( $query2 ) ){
				if(	dbGetNumRows() > 0 ){ 
					// user already play for today
				}else{
					$query = "INSERT INTO ".mysql_table_games." (email) VALUES ('".$email."')";
					if(dbQuery($query))
					{
						$instantWinners = array(41, 62, 73, 100, 120, 178, 180, 192,
						199, 200, 202, 210, 222, 230, 248, 267, 271, 278,
						280, 283, 290, 297, 299, 303, 315, 327, 335, 345,
						350, 367, 370, 375, 380, 384, 392, 398, 401, 407,
						415, 426, 434, 444, 456, 459, 471, 472, 474, 476,
						478, 480, 484, 490, 499, 500, 513, 518, 520, 521,
						522, 523, 525, 532, 540, 547, 558, 562, 567, 570,
						575, 585, 595, 599, 600, 605, 610, 615, 620, 630,
						635, 638, 640, 650, 660, 670, 677, 680, 690, 700, 
						
						3820, 3825, 3830, 3840, 3845, 3852, 3858, 3902, 3915, 3921, 3925, 3932, 3935, 3942, 3949, 3955, 3960, 3964, 3968, 3977, 3982, 3988, 3991, 3999, 4002,
						6375, 6380, 6382, 6388, 6390, 6392, 6396, 6400, 6402, 6405
						); 
						$newUserId = dbGetInsertId();
						if (in_array($newUserId, $instantWinners)){
							$myObj->insertGameContest = 0;
							$myObj->instantWin = 1;
							$query3 = "UPDATE ".mysql_table_games." SET instant_win = 1 WHERE id=".$newUserId."";
							dbQuery($query3);
						}else{
							$myObj->insertGameContest = 1;
							$myObj->instantWin = 0;
						}
					}

				}
			}
           
		}else{
			$myObj->logged = 0;
		}

        print json_encode( $myObj );
        break;

        case 'sendNotificationToAll':
        if( isset( $_POST['text'] ) ){
            $text = dbEscape($_POST['text']);
            $userIdArray = array();
            $query = 'SELECT fbUserId FROM '.mysql_table_users ;
            if(dbQuery($query))
            {
                while( $row = dbFetchAssoc() ){
                    $userIdArray[] = $row['fbUserId'];
                }
                $temp = sendNotification($text,$userIdArray);
            }
        }
        //
        print json_encode( $temp );
        break;

		default:
			print json_encode( $res );
	}
	
?>