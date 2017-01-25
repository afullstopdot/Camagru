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

}
