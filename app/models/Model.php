<?php

/*
** This model will interact with db_camagru | users & temp_users
*/

class Model
{

  /*
  ** This is the function i will use to encrypt passwords in the db
  ** break te password into an array hash each charcater using md5 and append it
  ** to the final password, finally we hash the final.
  */

  protected function password_hash($password)
  {
    $arr = str_split($password);

    foreach ($arr as $key) {
      $final .= md5($key);
    }
    return hash('whirlpool', $final);
  }

  protected function error_log($message)
  {
    $headers = 'From: Camagru Developer Team <andreantoniomarques19@gmail.com>' . 
    "\r\n" . 'X-Mailer: PHP/' . phpversion();
    return mail(ADMIN_EMAIL, 'Error on Camagru Website', $message, implode("\r\n", $headers));
  }
}
