<!-- Custom SweetAlert alert that gets triggered on email send -->
<script type="text/javascript">
	function enquirySent() {
		swal({
		  title: "Order Complete!",
		  text: "Be sure to check your emails for confirmation of your order details, as well as details about what content you need to provide for your new website.",
		  icon: "success",
		  button: "Aww yeah!",
		});
	}
	function enquiryNotSent() {
		swal({
		  title: "Oops!",
		  text: "Order was NOT submitted, because you never proved that you are not a robot! =(",
		  icon: "error",
		  button: "Dangit!",
		});
	};
</script>

<!-- The script to send the mail -->
<?php
    if (isset($_POST['submit'])) {

        // For the Google recaptcha
    	$curlx = curl_init();
		curl_setopt($curlx, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($curlx, CURLOPT_HEADER, 0);
		curl_setopt($curlx, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curlx, CURLOPT_POST, 1);
		$post_data = [
		    'secret' => 'YOUR CAPTCHA SECRET KEY',
		    'response' => $_POST['g-recaptcha-response']
		];
		curl_setopt($curlx, CURLOPT_POSTFIELDS, $post_data);
		$resp = json_decode(curl_exec($curlx));
		curl_close($curlx);
		// Google recaptcha end

		// Form details (sanitized)
        $name = htmlspecialchars($_POST['name']);
    	$surname = htmlspecialchars($_POST['surname']);
    	$email = htmlspecialchars($_POST['email']);
    	$message = htmlspecialchars($_POST['message']);

    	// Mail headers and details
    	$email_from = 'youremail@yourdomain.com';
    	$email_body = "You have received a new message from the user $name $surname.\nHere is the message:\n\n".$message;

		$headers = "From: $email_from \r\n";
		$headers .= "Reply-To: ".$email."\r\n";
		$headers .= "Return-Path: ".$email."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        $error = false;

        // Some more input validation/sanitizing
		if (!preg_match("/^[a-zA-Z ]*$/",$first_name) && $first_name!="") {
			$error = true; 
		}
		if (!preg_match("/^[a-zA-Z ]*$/",$last_name) && $last_name!="") {
			$error = true; 
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email!="") {
			$error = true;
		}

		function IsInjected($str) {
		    $injections = array('(\n+)',
		           '(\r+)',
		           '(\t+)',
		           '(%0A+)',
		           '(%0D+)',
		           '(%08+)',
		           '(%09+)'
		           );
		               
		    $inject = join('|', $injections);
		    $inject = "/$inject/i";
		    
		    if (preg_match($inject,$str)) {
		      return true;
		    } else {
		      return false;
		    }
		}

		if (IsInjected($visitor_email)) {
		    echo "Bad email value!";
		    exit;
		}

		// Sending the email
        if ($error == false) {
            $to = "youremail@yourdomain.com";
            $subject = "Enquiry from website";
            mail($to, $subject, $email_body, $headers);

            // Calling the email sent / not sent alerts
            echo '<script type="text/javascript">',
			    'enquirySent()',
				'</script>';
        } else {
        	echo '<script type="text/javascript">',
			    'enquiryNotSent()',
			    '</script>';
        }
    }
?>