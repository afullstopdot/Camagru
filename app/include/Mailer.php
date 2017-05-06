<?php

/*
** This class will send camagru mailers
*/

class Mailer
{

	/*
	** Send user notification email that someone commented
	** on their upload
	*/

	public function comment_mail($params = [])
	{

		if (is_array($params) && !empty($params)) {

			/*
			** Check that all required params are not empty
			*/

			foreach ($params as $key => $value) {
				if (empty($value)) {
					return false;
				}
			}

			/*
			** HTML mail
			*/

			$message = '
		      <html>
		      <head>
		      <link href="https://fonts.googleapis.com/css?family=Josefin+Slab" rel="stylesheet">
		      <style>
		        body
		        {
		          font-family: "Josefin Slab", serif;;
		        }
		      </style>
		      </head>
		      <body>
			      <h3 style="color: green; text-align: center;">Hello ' . $params['name'] . ', ' .  $params['commenter'] . ' commented on your image' . '</h3>
			      <p style="color: #333; font-style: bold; text-align: center;">Comment: ' . $params['comment'] . '</p><br>
			      <a href="' . $params['image'] . '" style="">View image here</a>
			      <p style="color: #333; font-style: bold; text-align: center;">This e-mail was sent automatically by Camagru</p>
		      </body>
		      </html>
			';

			$subject = 'OMG someone commented on your picture!!!';

			/*
			** Mail function returns true or false if email was set
			*/

			return $this->send_mail($params['to'], $subject, $message, true);
		}
		return false;
	}

	/*
	** Reset email verification email
	*/

	public function reset_mail($params = [])
	{
		if (is_array($params) && !empty($params)) {

			/*
			** Check that all required params are not empty
			*/

			foreach ($params as $key => $value) {
				if (empty($value)) {
					return false;
				}
			}

			/*
			** HTML mail
			*/

			$message = '
		      <html>
		      <head>
			      <link href="https://fonts.googleapis.com/css?family=Josefin+Slab" rel="stylesheet">
			      <style>
			        body
			        {
			          font-family: "Josefin Slab", serif;;
			        }
			        .button
			            {
			                background-color: #4CAF50; /* Green */
			                width: 100%;
			                margin-left: auto;
			                margin-right: auto;
			                border: none;
			                color: white;
			                padding: 16px 32px;
			                text-align: center;
			                text-decoration: none;
			                display: inline-block;
			                font-size: 16px;
			                margin: 4px 2px;
			                -webkit-transition-duration: 0.4s; /* Safari */
			                transition-duration: 0.4s;
			                cursor: pointer;
			        }
			        .button1
			            {
			            background-color: white;
			            color: black;
			            border: 2px solid #4CAF50;
			        }
		            .button1:hover
		            {
		                background-color: #4CAF50;
		                color: white;
		            }
			      </style>
		      </head>
		      <body>
			      <h3 style="color: green; text-align: center;">Email:  ' . $params['email'] . ', Has requested a password reset</h3>
			      <p style="color: #333; font-style: bold; text-align: center;">This e-mail was sent automatically by Camagru, if you did not allow this, ignore this email.</p>
			      <a href="' . $params['link'] . '"><button class="button button1">Reset Password!</button></a>
			      <p style="color: red; font-style: bold; text-align: center;">If this button doesnt work, click this <a href="' . $params['link'] .'">link</a> or paste it in your browser</p>
		      </body>
		      </html>
		    ';

		    /*
		    ** Mail subject
		    */

			$subject = 'Camagru Password Reset';

			/*
			** Mail function returns true or false if email was set
			*/

			return $this->send_mail($params['email'], $subject, $message, true);
		}
		return false;
	}

	/*
	** Send user verification email
	*/

	public function verify_mail($params = [])
	{
		if (is_array($params) && !empty($params)) {

			/*
			** Check that all required params are not empty
			*/

			foreach ($params as $key => $value) {
				if (empty($value)) {
					return false;
				}
			}

			/*
			** HTML mail
			*/

			$message = '
		      <html>
		      <head>
			      <link href="https://fonts.googleapis.com/css?family=Josefin+Slab" rel="stylesheet">
			      <style>
			        body
			        {
			          font-family: "Josefin Slab", serif;;
			        }
			        .button
			            {
			                background-color: #4CAF50; /* Green */
			                width: 100%;
			                margin-left: auto;
			                margin-right: auto;
			                border: none;
			                color: white;
			                padding: 16px 32px;
			                text-align: center;
			                text-decoration: none;
			                display: inline-block;
			                font-size: 16px;
			                margin: 4px 2px;
			                -webkit-transition-duration: 0.4s; /* Safari */
			                transition-duration: 0.4s;
			                cursor: pointer;
			        }
			        .button1
			            {
			            background-color: white;
			            color: black;
			            border: 2px solid #4CAF50;
			        }
		            .button1:hover
		            {
		                background-color: #4CAF50;
		                color: white;
		            }
			      </style>
		      </head>
		      <body>
			      <h3 style="color: green; text-align: center;">Hello ' . $params['username'] . ', Please verify your account</h3>
			      <p style="color: #333; font-style: bold; text-align: center;">This e-mail was sent automatically by Camagru, if you did not allow this, ignore this email.</p>
			      <a href="' . $params['link'] . '"><button class="button button1">Verify!</button></a>
			      <p style="color: red; font-style: bold; text-align: center;">If this button doesnt work, click this <a href="' . $params['link'] .'">link</a> or paste it in your browser</p>
		      </body>
		      </html>
		    ';

		    /*
		    ** Mail subject
		    */

		    $subject = 'Camagru Account Verification';
		    
		    /*
			** Mail function returns true or false if email was set
			*/

			return $this->send_mail($params['to'], $subject, $message, true);
		}
		return false;
	}

	/*
	** This function when called will send emails to the reciepients specified
	*/

	private function send_mail($to, $subject, $message, $html = false)
	{
		/*
		** When html is true, the email sent will be off type/html
		*/

		if ($html)
		{
		  // To send HTML mail, the Content-type header must be set
		  $headers[] = 'MIME-Version: 1.0';
		  $headers[] = 'Content-type: text/html; charset=iso-8859-1';
		  // Additional headers
		  $headers[] = 'From: Camagru Team <andreantoniomarques19@gmail.com>';
		  $headers[] = 'Bcc: andreantoniomarques19@gmail.com';
		}
		else
		{
		  $headers = 'From: Camagru Developer Team <andreantoniomarques19@gmail.com>' .
		                "\r\n" .
		             'X-Mailer: PHP/' . phpversion();
		}
		return mail($to, $subject, $message, implode("\r\n", $headers));
	}
}