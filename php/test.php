<?php  
$to = "herpaul2016@gmail.com";
 $subject = "hi" ;
 $body = "<div> hi hi .. </div>";

    $headers = 'From: YourLogoName info@domain.com' . "\r\n" ;
    $headers .='Reply-To: '. $to . "\r\n" ;
    $headers .='X-Mailer: PHP/' . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   
if(mail($to, $subject, $body,$headers)) {
  echo('<br>'."Email Sent ;D ".'</br>');
  } 
  else 
  {
  echo("<p>Email Message delivery failed...</p>");
  }


?>
