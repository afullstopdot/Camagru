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