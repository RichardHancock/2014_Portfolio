<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Richard Hancock: Portfolio - Contact</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
        
		<link href='http://fonts.googleapis.com/css?family=Chelsea+Market' rel='stylesheet' type='text/css'>
		
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>

        <?php

        //For ease of commiting to git without exposing these keys I am reading them in from text
        $keyFile = fopen(".dontDelete/recaptchaKeys.txt", "r") or die("Unable to open file!");

        //Settings
        $privatekey = fgets($keyFile); //Change to your own key
        $publickey = fgets($keyFile); //Change to your own key
        $mailTo = fgets($keyFile); // Change to your own email address
        //Didn't add error checking as this content will never change
        
        fclose($keyFile);

        //Removing blank character at end of strings
        $privatekey = substr($privatekey, 0, -1);
        $publickey = substr($publickey, 0, -1);
        $mailTo = substr($mailTo, 0, -1);

        // type of form input; isTextarea is a bool which handles a special case
        // TODO: There must be a better way to do this.
        function displayFormError($type, $isTextarea)
        {
            global $errors, $errorStrings, $formData;

            if($isTextarea) {
                if($errors[$type]) {
                    $returnString = 'class="error" placeholder="'.$errorStrings[$type].'"></textarea>';
                } else {
                    $returnString = 'class="formInput" placeholder="'.$type.'">'.$formData[$type].'</textarea>';
                }
            } else {
                if($errors[$type]) {
                    $returnString = 'class="error" placeholder="'.$errorStrings[$type].'">';
                } else {
                    $returnString = 'class="formInput" placeholder="'.$type.'" value="'.$formData[$type].'">';
                }
            }
            return $returnString;
        }


        if (isset($_POST["Submitted"]))
        {

            //Functions
            function hasContent($input)
            {
                global $errorCount;
                if (strlen($input) > 0) {
                    return true;
                }
                else
                {
                    $errorCount++;
                    return false;
                }
            }

            function isValidEmail($input)
            {
                global $errorCount;
                // This should accept most emails but might not accept rarer email address formats.
                if (preg_match('/^([A-Z0-9\.\-_]+)@([A-Z0-9\.\-_]+)?([\.]{1})([A-Z]{2,6})$/i', $input))
                {
                    return true;
                }
                else
                {
                    $errorCount++;
                    return false;
                }
            }
            //Initialize Error bool array
            $errors = array(
                'Name' => false,
                'Email' => false,
                'Subject' => false,
                'Message' => false,
                'Captcha' => false,
            );
            //Initialize error message array
            $errorStrings = array(
                'Name' => "Please insert your name",
                'Email' => "Please insert a valid email address",
                'Subject' => "Please insert a subject",
                'Message' => "Please insert a message",
                'Captcha' => "", //Gets generated later by the captcha php
            );

            //Recaptcha required code:
            require_once('recaptchalib.php');
            $resp = recaptcha_check_answer ($privatekey,
                $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"],
                $_POST["recaptcha_response_field"]
            );

            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                //die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
                //"(reCAPTCHA said: " . $resp->error . ")");
                $errors['Captcha'] = true;

                //Bit lazy but couldn't find a reliable source stating the error codes, but this is good enough for users of the form
                if ($resp->error == "incorrect-captcha-sol") {
                    $errorStrings['Captcha'] = "Incorrect captcha was entered";
                } else {
                    $errorStrings['Captcha'] = "An unknown server error within captcha has occurred!";
                    error_log("Server Captcha Error occured",0); //Log an event
                }
            }

            //Fetch Value from the forms POST data
            $formData = array(
                'Name' => $_POST["Name"],
                'Email' => $_POST["Email"],
                'Subject' => $_POST["Subject"],
                'Message' => $_POST["Message"],
            );

            //Error checking section
            $errorCount = 0;

            $errors['Name'] = !hasContent($formData['Name']);
            $errors['Email'] = !hasContent($formData['Email']) || !isValidEmail($formData['Email']);
            $errors['Subject'] = !hasContent($formData['Subject']);
            $errors['Message'] = !hasContent($formData['Message']);
            var_dump($errorCount);
            // Final check and Sending the Email
            if ($errorCount == 0) {
                //Email Construction
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$formData['Email']."\r\n";
                $headers .= 'Reply-To: '.$formData['Email']."\r\n";

                $body = "<html><body><b>Name:</b>".$formData['Name']."<br>";
                $body .= "<b>Email:</b>".$formData['Email']."<br>";
                $body .= "<b>Message:</b>"."<br>";
                $body .= $formData['Message']."<br></body></html>";

                //CF is a tag you can set you mail program to auto allow
                $finalSubject = "CF: ".$formData['Subject'];

                // Show a message based on if the email sent or not
                $result = 0;
                if (mail($mailTo, $finalSubject, $body, $headers)) {
                    $result = 1;
                } else {
                    $result = 2;
                }

            }
        }
        ?>

        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

		<div class="wrapper">
			<div class="header">
				<div class="headerTitle">
					<span>Richard Hancock - Games Programmer</span>
				</div>
				<div class="nav">
					<ul>
                        <a href="index.html">
                            <li>Home</li>
                        </a>
                        <li onclick="window.location = 'portfolio.html'">
                            <a href="portfolio.html">Portfolio</a>
                            <ul>
                                <a href="portfolio/programming.html">
                                    <li>Programming</li>
                                </a>
                                <a href="portfolio/web.html">
                                    <li>Web Development</li>
                                </a>
                                <a href="portfolio/other.html">
                                    <li>Other Skills</li>
                                </a>
                            </ul>
                        </li>
                        <a href="about.html">
                            <li>About</li>
                        </a>
                        <a href="#" onclick="alert('Coming Soon')">
                            <!--Future-->
                            <li>Blog</li>
                        </a>
                        <a href="cv.html">
                            <li>CV</li>
                        </a>
                        <a href="contact.php">
                            <li>Contact</li>
                        </a>
                    </ul>
				</div>
			</div>
			<div class="mainContent">
				<div class="socialMedia">
					<h2>Social Media</h2>
					<a href="https://www.linkedin.com/in/hancockrichard" target="_blank">
						<img src="res/LinkedIn.png" alt="LinkedIn Profile" title="My LinkedIn Profile">
					</a>
					<a href="https://github.com/RichardHancock" target="_blank">
						<img src="res/GitHub.png" alt="GitHub Profile" title="My GitHub Profile">
					</a>
					<a href="https://www.youtube.com/user/SpaceCrazyProduction" target="_blank">
						<img src="res/YouTube.png" alt="YouTube Profile" title="My YouTube Profile">
					</a>
					<!--Icons From: http://www.dreamstale.com/free-download-72-vector-social-media-icons/ -->
				</div>
				<div class="contactRightAligned">
					<h2>Contact Me</h2>
					<p>
						Feel free to contact me using the provided contact form
						any comments or queries.
					</p>
				</div>

                <script type="text/javascript">
                    //Changes the theme for Recaptcha
                    var RecaptchaOptions = {theme : 'clean'};
                </script>
				<form name="contact" action="contact.php" method="post">
    				<div>
                        <div class="left">
    						<input required type="text" name="Name" <?php echo(displayFormError('Name',false)); ?>
    						<input required type="email" name="Email" <?php echo(displayFormError('Email',false)); ?>
                            <div>
                                <?php
                                    require_once('recaptchalib.php');
                                    echo recaptcha_get_html($publickey);
                                ?>
                            </div>
    					</div>
    					<div class="right">
                            <input required type="text" name="Subject" <?php echo(displayFormError('Subject',false)); ?>
    						<textarea required name="Message" <?php echo(displayFormError('Message',true)); ?>
    					</div>
                    </div>
					<span class="error">
                        <?php
                        if($errors['Captcha']) {
                            echo($errorStrings['Captcha']);
                        } elseif ($errorCount != 0) {
                            echo("Please fix any mistakes and resubmit");
                        } elseif ($result == 2) {
                            echo("Internal Server Error: Email did not send");
                        } elseif ($result == 1) {
                            echo("Email was sent successfully!");
                        }
                        ?>
					</span>
                    <input type="hidden" name="Submitted" value=1>
					<input class="button" type="submit" value="Send">
				</form>
			</div>
			<div class="footer">
				<span>Richard Hancock &copy; 2014</span>
			</div>
		</div>
		
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>

        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='http://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-36113290-3');ga('send','pageview');
        </script>
    </body>
</html>
