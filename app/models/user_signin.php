<?php

require_once 'Model.php';

class user_signin extends Model
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
  ** This function will authenticate a user, if it is a valid user a array
  ** with the users information will be returned, if not false will be returned
  */

  public function authenticate($email, $password)
  {
    if (isset($email) && isset($password) && isset($this->db))
    {
      try
      {

        $stmt = $this->db->prepare(
          'SELECT user_id, email, username, picture, joined
          FROM users
          WHERE email = :email
          AND password = :password'
        );

        $stmt->execute([
          'email' => $email,
          'password' => $this->password_hash($password)
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
      }
      catch (PDOException $e)
      {
        // return (['validate username error: ' => $e->getMessage()]);//debugging
        return (false);
      }
    }
  }

  /*
  ** For users that registered with oauth we authenticate the email and see if
  ** said email is an authorized oauth email account meaning, users cant log in
  ** with this email withount oauth
  */

  public function oauth_authenticate($email)
  {
    if (isset($email) && isset($this->db))
    {
      try
      {

        $stmt = $this->db->prepare(
          'SELECT user_id, email, username, picture, joined
          FROM users
          WHERE email = :email
          AND password = 1'
        );

        $stmt->execute([
          'email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
      }
      catch (PDOException $e)
      {
        // return (['validate username error: ' => $e->getMessage()]);//debugging
        return (false);
      }
    }
  }
}
