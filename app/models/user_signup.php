<?php

require_once 'Model.php';


/*
** This model will interact with db_camagru | users & temp_users
** This model will be used for user signup interaction with db
*/

class user_signup extends Model
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
  ** This function will check if a email verification is valid
  ** and return true or false respectiveley
  */

  public function check_verify($username, $verification)
  {
    if (isset($this->db) && isset($username) && isset($verification))
    {
      try
      {

        /*
        ** if the sql statement is true, that means verification is valid
        */

        $stmt = $this->db->prepare(
          'SELECT * FROM unverified_users
           WHERE username = :username
           AND verification = :verification'
        );

        $stmt->execute([
          'username' => $username,
          'verification' => $verification
        ]);

        /*
        ** if the result of the exection is false that means verification failed
        ** otherwise if true an array will be retrned containing the record
        ** to be moved into the users table
        */

        return $stmt->fetch(PDO::FETCH_ASSOC);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signup.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> check_verify()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }

  /*
  ** this function will create a permanent user (post verification)
  */

  public function create_perm_account($email, $username, $password, $picture = 'N/A')
  {
    if (isset($email) && isset($username) && isset($password))
    {
      try
      {
        /*
        ** Insert into perm users table
        */

        $stmt = $this->db->prepare(
          'INSERT INTO users (email, username, password, picture)
           VALUES (:email, :username, :password, :picture)'
        );

        $stmt->execute([
          'email' => $email,
          'username' => $username,
          'password' => $password,
          'picture' => $picture
        ]);

        /*
        ** Remove from unverified users after inserting into perm users
        */

        $stmt = $this->db->prepare(
          'DELETE FROM unverified_users
           WHERE username = :username
           AND email = :email'
        );

        $stmt->execute([
          'username' => $username,
          'email' => $email
        ]);

        return (true);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signup.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> create_perm_account()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }

  /*
  ** This function will add a user to temp_users, send a verification email
  ** with a verificatio code
  */

  public function create_temp_account($email, $username, $password, $veri)
  {
    if (isset($email) && isset($username) &&
        isset($password) && isset($this->db) && isset($veri))
    {
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
          'password' => $this->password_hash($password),
          'verification' => $veri
        ]);

        return (true);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signup.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> create_temp_account()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }

  /*
  ** This function will validate the username and e-mail (dont have any in the db)
  ** will return an array with list of errors if any.
  */

  public function validate_details($email, $username)
  {
    $response = [
      'email' => 'OK',
      'username' => 'OK'
    ];

    if ($this->perm_email_exists($email)) {
      $response['email'] = 'The e-mail is taken already!';
    }
    if ($this->perm_username_exists($username)) {
      $response['username'] = 'The username is taken already!';
    }
    if ($this->temp_username_exists($username)) {
      $response['username'] = 'Account pending verification!';
    }
    if ($this->temp_email_exists($email)) {
      $response['email'] = 'Account pending verification!';
    }
    return ($response);
  }

  /*
  ** This function will check the db to see if the username is taken (temp users)
  */

  private function temp_username_exists($username)
  {
    if (isset($username) && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM unverified_users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        // return (['validate username error: ' => $e->getMessage()]);//debugging
        return (false);
      }
    }
  }

  /*
  ** Thisn function will check the db to see if the email is taken (temp users)
  */

  private function temp_email_exists($email)
  {
    if (isset($email) && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM unverified_users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        //return (['validate email error: ' => $e->getMessage()]);
        return (false);
      }
    }
  }

  /*
  ** This function will check the db to see if the email is taken (permm users)
  */

  private function perm_email_exists($email)
  {
    if (isset($email) && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        // return (['validate email error: ' => $e->getMessage()]);//debugging
        return (false);
      }
    }
  }

  /*
  ** This function will check the db to see if the username is taken (perm users)
  ** i made this function as ooposed to the other helpers public, just because
  ** some of our oauth registrations dont pass a unique username so i generate
  ** one for the user, i want to check that the uniquely generated username
  ** isnt taken and try again.
  */

  public function perm_username_exists($username)
  {
    if (isset($username) && isset($this->db))
    {
      try
      {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        // return (['validate username error: ' => $e->getMessage()]);//debugging
        return (false);
      }
    }
  }
}
