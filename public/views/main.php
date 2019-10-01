<?php
	if(!defined('Iznogoud')) {
	    header('HTTP/1.0 404 Not Found');
	    echo "<h1>404 Not Found</h1>";
	    echo "The page that you have requested could not be found.";
	    exit();
  }
      session_start();
      // session_destroy(); // uncomment for force logout 

      
?>

<!DOCTYPE html>
<html >
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#"> <!-- Mobile Chrome toolbar color -->
    <meta property="og:image" content="<?=shareImage;?>" />
    <title>PAPADOPOULOU</title>
    <link rel="apple-touch-icon" href="images/favicon.png">
    <link rel="icon" href="images/favicon.png">
    <link rel="image_src" type="image/jpeg" href="<?=shareImage;?>" />
    <link rel="stylesheet" type="text/css" href="<?=serverUrl;?>css/style.css?<?=(DEBUG?rand():cacheVersion);?>">

    <link rel="stylesheet" href="css/anim_style.css">


    <link rel="stylesheet" type="text/css" href="<?=serverUrl;?>css/responsive.css?<?=(DEBUG?rand():cacheVersion);?>">
    <script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
        <script type="text/javascript" src="<?=serverUrl;?>js/iznogoud.js?<?=(DEBUG?rand():cacheVersion);?>"></script>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

<!-- Facebook Pixel Code -->


<noscript>

<img height="1" width="1"

src="https://www.facebook.com/tr?id=219902615188843&ev=PageView

&noscript=1"/>

</noscript>

<!-- End Facebook Pixel Code -->



    
  <meta charset="UTF-8">  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" href="images/favicon.png">
  <link rel="icon" href="images/favicon.png">


  <meta http-equiv="Content-Style-Type" content="text/css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

</head>

<body>

<!--register-->

<?php if (!isset($_SESSION['registeredUser']) && !isset($_SESSION['loggedUser'])) { ?>

<form id="register_form">

        <h1 style="padding: 3%; font-weight: 300;">Εγγραφή</h1>
        <input type="text" class="fullName_register" placeholder="Το Ονοματεπώνυμο σου"> <br>
        <input type="text" class="email_register" placeholder="Το Email σου"> <br>
        <input type="number" class="phone_register" placeholder="Το τηλέφωνό σου"> <br>
        <input type="password" class=" password_register" placeholder="Ο κωδικός σου"> <br>
        <input type="password" class="password_confirm_register" placeholder="Επανάληψη κωδικού">
             
        <p style="text-align: left; padding: 0px 10%;">
          <input class="terms-check" type="checkbox" id="checkbox-1" name="checkbox"/>
          <label for="checkbox-1">Αποδέχομαι τους όρους του διαγωνισμού</label>  <br>
          <input class="newsletter-check" type="checkbox" id="checkbox-2"/>
          <label for="checkbox-2">Newsletter</label>
        </p> 

        <div id="register_recaptcha" class="g-recaptcha" data-sitekey="6Lcv-LoUAAAAAC_Qs8MAHEME8brrAPUYlgIuywQ0"></div><br>

        <button id="register-btn" style="cursor:pointer;">Εγγραφή</button>
    
</form> 

<br>

<form id="login_form" style="text-align:left;">
    <h1 style="padding: 3%; font-weight: 300;">Σύνδεση</h1>
    <input type="email" name="email_login" id="email_login" placeholder="email"><br>
    <input type="password" name="password_login" id="password_login" placeholder="password"><br><br>
    <div id="login_recaptcha" class="g-recaptcha" data-sitekey="6Lcv-LoUAAAAAC_Qs8MAHEME8brrAPUYlgIuywQ0"></div><br><br>
    <button id="login-btn" style="cursor:pointer;">Σύνδεση</button>
</form>
   

<?php } else { ?>
  <form  id="logout-button">
    <button style="cursor:pointer;" id="logOut">Logout</button>
  </form>
  <br><br>
  <?php echo $_SESSION["email"]; ?>
  <h2>Κωδικός</h2>
  <form id="submit-code">
    <input type="text"  id="input-code"> <br>
    <button style="cursor:pointer;" id="sumbitCode">Υποβολή</button>
  </form>

  <?php } ?>


  <script>
    var onloadCallback = function() {
    $('.g-recaptcha').each(function(index, el) {
      var widgetId = grecaptcha.render(el, {'sitekey': '6Lcv-LoUAAAAAC_Qs8MAHEME8brrAPUYlgIuywQ0'});
      $(this).attr('data-widget-id', widgetId);
    });
  };
  </script>
  <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>
</body>
</html>

