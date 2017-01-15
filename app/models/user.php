<?php

/*
** This model will interact with db_camagru | users & temp_users
*/

class user
{
  private $db;

  /*
  ** when the Controller creates a new instance of this class
  ** the pdo object is passed by the constructor and set here.
  */

  public function __construct($db)
  {
    if (!isset($this->db))
      $this->db = $db;
  }

  /*
  ** This function will add a user to temp_users, send a verification email
  ** with a verificatio code
  */

  public function create_temp_account($email, $username, $password)
  {
    if (isset($email) && isset($username) && isset($password) && isset($this->db))
    {
      $verification = hash('whirlpool', mt_rand(50, 100)); //check get for param limit

      /*
      ** info to be used when e-mail for verification is sent.
      */

      $link = SITE_URL . '/auth/verify/uid=' . base64_encode($username) . '/code=' . $verification;
      $subject = 'Camagru Account Verification';
      $body_header = 'please verify your account';
      $body_button = 'Verify!';

      try
      {
        /*
        ** Insert into table, then send email
        */

        $stmt = $this->db->prepare('
          INSERT INTO unverified_users (email, username, password, verification)
          VALUES (:email, :username, :password, :verification)
        ');

        $stmt->execute([
          'email' => $email,
          'username' => $username,
          'password' => $password,
          'verification' => $verification
        ]);

        if ($this->send_mail($username, $email, $subject, $body_header, $body_button, $link) == false)
          return ['create account error' => 'Failed to send mail.'];
        return (true);
      }
      catch (PDOException $e)
      {
        return (['create account error' => $e->getMessage()]);
      }
    }
  }

  /*
  ** This function will validate the username and e-mail (dont have any in the db)
  ** will return an array with list of errors if any.
  ** This function will also do additional validation checks
  */

  public function reg_response($email, $username)
  {
    $response = [
      'email' => 'OK',
      'username' => 'OK'
    ];

    if ($this->email_exists($email) == true)
      $response['email'] = 'The e-mail is taken already!';
    if ($this->username_exists($username) == true)
      $response['username'] = 'The username is taken already!';
    return ($response);
  }

  /*
  ** This function will check the db to see if the email is taken
  */

  private function email_exists($email)
  {
    if ($email && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(array('email' => $email));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        return (['validate email error: ' => $e->getMessage()]);
      }
    }
  }

  /*
  ** This function will check the db to see if the username is taken
  */

  private function username_exists($username)
  {
    if (isset($username) && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(array('username' => $username));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        return (['validate username error: ' => $e->getMessage()]);
      }
    }
  }

  /*
  ** This function will send all camagru emails to the accounts specified
  */

  private function send_mail($username, $recipient, $subject, $h3, $button, $link)
  {
    $headers = 'From: Camagru Developer Team <andreantoniomarques19@gmail.com>' . "\r\n" .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $mail_body = $this->get_mail_body($h3, $button, $link, $username);
    return mail($recipient, $subject, $mail_body, $headers);
  }

  /*
  ** returns an html formatted message for use by sen mail
  */

  private function get_mail_body($h3, $button, $link, $username)
  {
    return '
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

      <h3 style="color: green; text-align: center;">Hello ' . $username . ', ' . $h3 . '</h3>
      <p style="color: #333; font-style: bold; text-align: center;">This e-mail was sent automatically by Camagru, if you did not allow this, ignore this email.</p>
      <a href="' . $link . '"><button class="button button1">' . $button . '</button></a>
      <p style="color: red; font-style: bold; text-align: center;">If this button doesnt work, click this <a href="' . $link .'">link</a> or paste it in your browser</p>

      </body>
      </html>
    ';
  }
}
