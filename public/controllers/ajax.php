<?php
$db = mysqli_connect('localhost','root','','papadopoulou');

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
							$query = "INSERT INTO ".mysql_table_users." (fullName, email,  password, mobile, newsletter, created) VALUES ('".$full_name."', '".$email."', '".$password."', '".$phone."',  '".$newsletter."', NOW() )";
							dbQuery($query);
							echo "register_success";
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
					$query2 = "SELECT * FROM ".mysql_table_users." WHERE email = '".$email."' LIMIT 1";
								
					$result = mysqli_query($db, $query2);
					$row = mysqli_fetch_assoc($result);

					if (password_verify($password, $row['password'])) {
						echo "rightCrendetials";
						$_SESSION['email'] = $email;
					} else {
						echo "wrongCrendetials";
					}
				}
			
			}
		break;


		case 'sumbitCode':


        $instant_win = 0;
		$print = -1;

        $code = mysqli_real_escape_string($db, $_POST['code']);
		$user = mysqli_real_escape_string($db, $_SESSION['email']);
		
        if(isset($code) && strlen($code) == 8 && isset($user) && strlen($user) > 2){
            $code = str_replace('<', "", $code);
            $code = str_replace('>', "", $code);
            $code = str_replace(".", "", $code);
            $code = str_replace(":", "", $code);
            $code = str_replace("/", "", $code);
            $code = str_replace("\\", "", $code);
            $code = str_replace("-", "", $code);
            $code = str_replace("'", "", $code);
            $code = str_replace('"', "", $code);
            $code = str_replace(" ", "", $code);
            $code = str_replace("a", "A", $code);
            $code = str_replace("α", "A", $code);
            $code = str_replace("Α", "A", $code);
            $code = str_replace("b", "B", $code);
            $code = str_replace("β", "B", $code);
            $code = str_replace("Β", "B", $code);
            $code = str_replace("c", "C", $code);
            $code = str_replace("ψ", "C", $code);
            $code = str_replace("d", "D", $code);
            $code = str_replace("δ", "D", $code);
            $code = str_replace("Δ", "D", $code);
            $code = str_replace("e", "E", $code);
            $code = str_replace("ε", "E", $code);
            $code = str_replace("Ε", "E", $code);
            $code = str_replace("f", "F", $code);
            $code = str_replace("φ", "F", $code);
            $code = str_replace("Φ", "F", $code);
            $code = str_replace("g", "G", $code);
            $code = str_replace("γ", "G", $code);
            $code = str_replace("Γ", "G", $code);
            $code = str_replace("h", "H", $code);
            $code = str_replace("η", "H", $code);
            $code = str_replace("Η", "H", $code);
            $code = str_replace("i", "I", $code);
            $code = str_replace("ι", "I", $code);
            $code = str_replace("Ι", "I", $code);
            $code = str_replace("j", "J", $code);
            $code = str_replace("ξ", "J", $code);
            $code = str_replace("Ξ", "J", $code);
            $code = str_replace("k", "K", $code);
            $code = str_replace("κ", "K", $code);
            $code = str_replace("Κ", "K", $code);
            $code = str_replace("λ", "L", $code);
            $code = str_replace("Λ", "L", $code);
            $code = str_replace("l", "L", $code);
            $code = str_replace("m", "M", $code);
            $code = str_replace("μ", "M", $code);
            $code = str_replace("Μ", "M", $code);
            $code = str_replace("n", "N", $code);
            $code = str_replace("ν", "N", $code);
            $code = str_replace("Ν", "N", $code);
            $code = str_replace("o", "O", $code);
            $code = str_replace("ο", "O", $code);
            $code = str_replace("Ο", "O", $code);
            $code = str_replace("p", "P", $code);
            $code = str_replace("π", "P", $code);
            $code = str_replace("Π", "P", $code);
            $code = str_replace("q", "Q", $code);
            $code = str_replace("ρ", "R", $code);
            $code = str_replace("Ρ", "R", $code);
            $code = str_replace("r", "R", $code);
            $code = str_replace("s", "S", $code);
            $code = str_replace("ς", "S", $code);
            $code = str_replace("σ", "S", $code);
            $code = str_replace("Σ", "S", $code);
            $code = str_replace("t", "T", $code);
            $code = str_replace("τ", "T", $code);
            $code = str_replace("Τ", "T", $code);
            $code = str_replace("u", "U", $code);
            $code = str_replace("υ", "U", $code);
            $code = str_replace("v", "V", $code);
            $code = str_replace("w", "W", $code);
            $code = str_replace("ω", "W", $code);
            $code = str_replace("x", "X", $code);
            $code = str_replace("χ", "X", $code);
            $code = str_replace("Χ", "X", $code);
            $code = str_replace("y", "Y", $code);
            $code = str_replace("Υ", "Y", $code);
            $code = str_replace("z", "Z", $code);
            $code = str_replace("ζ", "Z", $code);
			$code = str_replace("Ζ", "Z", $code);
			
            $seql = "SELECT `code` FROM `registers` WHERE `code` =  '$code'";
			$exists = mysqli_query($db,$seql);
			$num_rows1 = mysqli_num_rows($exists);
			
            //if not registered code
            if(!$num_rows1){ 

				$sql = "SELECT * FROM `codes` WHERE `code` =  '$code'";
				$res = mysqli_query($db, $sql);
				$num_rows = mysqli_num_rows($res);
				$res2 = mysqli_fetch_assoc($res);

				//if exists in codes
				if($num_rows) {
					$print = $res2['gift'];             
					$msql = "INSERT INTO `registers`(`usermail`, `code`, `instant_win`) VALUES ('".$user."', '".$code."', '".$print."')"; //insert into register 
					mysqli_query($db, $msql);
					$gifts = 0;

					if($print > 0){//if instant win.. instant winner mail notification
						$res3 = mysqli_query($db, "SELECT * FROM `users` WHERE `email` = '$user'");
						
						

						
						switch($print){
							case 1: $gifts = 'Καραόκε Μικρόφωνο';
							echo "Κέρδισες Καραόκε Μικρόφωνο";
							break;
							case 2: $gifts = 'GoPro Camera';
							echo "Κέρδισες GoPro Camera";
							break;
							case 3: $gifts = 'Polaroid Camera';
							echo "Κέρδισες Polaroid Camera";
							break;
							case 4: $gifts = 'Netflix Δωροκάρτα';
							echo "Κέρδισες Netflix Δωροκάρτα";
							break;
							case 5: $gifts = 'Ηχείο AKAI Bluetooth';
							echo "Κέρδισες Ηχείο AKAI Bluetooth";
							break;
							default: $gifts = 'no';
							echo "Δεν κέρδισες";
							break;
						};
						
                    	$res3 = mysqli_fetch_assoc($res3);
						// $from = '2plogemista@socialab.gr';
						// $preferences = ['input-charset' => 'UTF-8', 'output-charset' => 'UTF-8'];
						// $subject ='Instant Winner!';
						// $headers = "MIME-Version: 1.0"."\r\n";
						// $headers .= 'X-Mailer: PHP/'.phpversion();
						// $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
						// $headers .= "From: $from";
						// $encoded_subject = iconv_mime_encode('Subject', $subject, $preferences);
						// $encoded_subject = substr($encoded_subject, strlen('Subject: '));
						// switch($res3['adult']){
						// 	case 0: $adult = 'Ανήλικος';
						// 	break;
						// 	case 1: $adult = 'Ενήλικος';
						// 	break;
						// }
						// $text = "Ο ".$res3['fullName']." κέρδισε ".$gifts." e-mail: ".$res3['email']." : ".$res3['mobile']." Κωδικός: ".$code;
						// mail('2plogemista@socialab.gr',$encoded_subject,$text,$headers);
						$_SESSION['userEnterCode'] = true;
                } else {
					echo  "Ο κωδικός δεν κερδίζει";
					$_SESSION['userEnterCode'] = true;
				}               
			} else {
				echo "Ο κωδικός δεν υπάρχει";
			}
    	} else {
			echo "Ο κωδικός έχει χρησιμοποηθεί";
		}
    }; // if isset code and session user
       // print trim(json_encode($print, JSON_FORCE_OBJECT),'"');
		break;
		
		case 'sumbitForGift':
			$email = $_SESSION['email'];

			$query2 = "SELECT * FROM usersforgift WHERE email = '".$email."'";
			if( dbQuery( $query2 ) ){
				if(	dbGetNumRows() > 0 ){
					echo "already_played";
				} else {
					$query = "INSERT INTO usersforgift (email, date) VALUES ('".$email."', NOW() )";
					if (dbQuery($query)) {
						echo "game_over";

					} else {
						echo "game_problem";
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