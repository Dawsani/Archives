<?php

    function send_email($recipient, $resetToken) {
    
        $fromName = "The Archives";
        $subject = "Password Reset";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: $fromName <dawson.dwm@gmail.com>" . "\r\n";

        $msg = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Archives Password Reset</title>
            </head>
            <body>
                <h1>Password Reset</h1>
                <p>Here is your password reset code as requested:</p><br>
                <h2>{$resetToken}</h2><br>
                <p>If you did not request this password reset, let Dawson know.</p>
            </body>
        </html>
        HTML;

        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg,70);

        // send email
        mail($recipient, $subject, $msg, $headers);
    }
?>