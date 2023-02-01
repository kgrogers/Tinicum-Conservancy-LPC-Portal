<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        $_SESSION['loggedin'] = "no";
    } else {
        $_SESSION['loggedin'] = "yes";
        return false;
    }

    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $username = $_SESSION['username'];

    $to = $email;
    $subject = 'Your Tinicum Conservancy LPC Portal Username';
    $message = "Hi ".$fname.":\n\n";
    $message .= "Your username is: ".$username."\n\nIf you have any issues logging in please contact someone at Tinicum Conservancy.\n\n";
    $message .= "The Tinicum Conservancy LPC Portal Team";
    $headers = "From: lpcadmin@tinicumconservancy.org\r\n" .
               "Reply-To: noreply@tinicumconservancy.org\r\n" .
               "X-Mailer: PHP/" . phpversion();
    mail($to,$subject,$message,$headers);
    return
?>
