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
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signin.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> authenticate()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
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
          "SELECT user_id, email, username, picture, joined
          FROM users
          WHERE email = :email
          AND password = :password"
        );

        $stmt->execute([
          'email' => $email,
          'password' => 'N/A'
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signin.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> oauth_authenticate()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }

    /*
  ** This function will check the database to see if an account exists and can be reset
  ** An account can only be reset if the email is valid and it is not an oauth account.
  */

  public function validate_account($email)
  {
    if (isset($email))
    {
      try
      {
        $stmt = $this->db->prepare('
          SELECT email 
          FROM users 
          WHERE email = :email 
          AND password != :password
        ');

        $stmt->execute([
          'email' => $email,
          'password' => 'N/A'
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
          return (false);
        return (true);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signin.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> validate_account()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }

  /*
  ** This function will insert and also validate the reset token
  */

  public function reset_token($email, $token, $insert = true)
  {
    if (isset($email) && isset($token))
    {
      try
      {
        if ($insert === true)
        {
          $stmt = $this->db->prepare('
            UPDATE users
            SET reset = :reset
            WHERE email = :email
          ');
        }
        else
        {
          $stmt = $this->db->prepare('
            SELECT * 
            FROM users
            WHERE email = :email
            AND reset = :reset
          ');
        }

        if ($insert === false)
        {
          $stmt->execute([
            'email' => $email,
            'reset' => $token
          ]);

          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($result === false) 
          {
            return false;
          }
          else
          {
            $stmt = $this->db->prepare('
              UPDATE users
              SET reset = "expired"
              WHERE email = :email
              AND reset = :reset
            ');
          }
        }

        return $stmt->execute([
          'email' => $email,
          'reset' => $token
        ]);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signin.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> reset_token()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return false;
      }
    }
  }

  /*
  ** This function will update a password
  */

  public function reset_password($email, $password)
  {
    if (isset($email) && isset($password))
    {
      try
      {
        $stmt = $this->db->prepare('
          UPDATE users
          SET password = :password
          WHERE email = :email
        ');

        return $stmt->execute([
          'email' => $email,
          'password' => $this->password_hash($password)
        ]);
      }
      catch (PDOException $e)
      {
        $this->error_log('
          <p>Model:<b style="color: cyan; font-size: 18px;"> user_signin.php</b></p><hr>
          <p>Function:<b style="color: green; font-size: 18px;"> reset_password()</b></p><hr>
          <p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
        );
        return (false);
      }
    }
  }
}
