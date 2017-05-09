<?php

class Controller
{
  private static $db;

  /*
  ** This function will create a new instance of a model
  ** return it and allow communication with the database
  */

  protected function model($model)
  {
    if (file_exists(ROOT_DIR . '/app/models/' . $model . '.php'))
    {
      require_once ROOT_DIR . '/app/models/' . $model . '.php';
      return new $model(self::$db);
    }
    return NULL;
  }

  /*
  ** This function will create a new instance of a class
  ** in the include folder, these are helper functions
  */

  protected function helper($helper)
  {
    if (file_exists(ROOT_DIR . '/app/include/' . $helper . '.php'))
    {
      require_once ROOT_DIR . '/app/include/' . $helper . '.php';
      return new $helper;
    }
    return NULL;
  }

  /*
  ** This function will render a view, if it exists
  */

  protected function view($view, $data = [])
  {
    if (file_exists(ROOT_DIR . '/app/views/' . $view . '.php'))
    {
      require_once ROOT_DIR . '/app/views/' . $view . '.php';
    }
  }

  /*
  ** Redirect users to home page
  */

  protected function redirect($url = '')
  {
    if ($url === '')
    {
      header('Location: index.php');
    }
    else
    {
      header('Location: ' . $url);
    }
    exit;
  }

  /*
  ** Because a instance of type controller is never instantiated, this function
  ** can get the current pdo object db (static methods do not need an instance
  ** to be called)
  */

  public static function getDB()
  {
    return self::$db;
  }

  /*
  ** This function when called will assign an pdo obect to this controllers
  ** db attribute, if i has not been set
  */

  public static function setDB($db)
  {
    if (isset($db))
      self::$db = $db;
  }

  /*
  ** This function will update the flash variable in the session, to update users
  ** if true flash_message can be used with a redirection aswell
  */

  protected function flash_message($message, $alert, $redirect = '')
  {
    $_SESSION['flash'] = [$alert => $message];

    if ($redirect !== '')
    {
      $this->redirect($redirect);
    }
  }

  /*
  ** This function will check if a user is logged in or not and will return
  ** true or false respectively
  */

  protected function valid()
  {
    return isset($_SESSION['user']['user_id']) ? true : false;
  }

  // /*
  // ** Return the current user for this session
  // */

  // protected function user()
  // {
  //   if ($this->valid()) {
  //     return $_SESSION['user'];
  //   }
  //   return false;
  // }

  /*
  ** Return the current user for this session s an object
  */

  protected function user()
  {
    if ($this->valid()) {
      return (object) $_SESSION['user'];
    }
    return false;
  }

  /*
  ** Create random bytes to be used for crsf protection
  */

  protected function generate_tok()
  {
    return $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
  }

  /*
  ** Verify whether a token is indeed a valid generated token
  */

  protected function check_tok($token)
  {
    if (isset($_SESSION['token']) && $token === $_SESSION['token'])
    {
      unset($_SESSION['token']);
      return true;
    }
    return false;
  }
}