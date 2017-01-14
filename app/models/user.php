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
    if ($email)
    {
      if (isset($this->db))
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(array('email' => $email));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
    }
  }

  /*
  ** This function will check the db to see if the username is taken
  */

  private function username_exists($username)
  {
    if ($username)
    {
      if (isset($this->db))
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(array('username' => $username));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
    }
  }

  /*
  ** This function will send all camagru emails to the accounts specified
  */

  private function send_mail($to, $subject, $message, $headers)
  {

  }
}
