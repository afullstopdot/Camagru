<?php

class auth extends Controller
{
  /*
  ** Render view for google+ oauth
  */

  public function googlesignup($params = [])
  {
    $this->view('home/index', $params);
  }

  /*
  ** Render view for 42 oauth
  */

  public function fourtytwosignup($params = [])
  {
    $this->view('home/index', $params);
  }

  /*
  ** Render view for reset
  */

  public function reset($params = [])
  {
    $this->view('auth/reset', $params);
  }

  /*
  ** Render view for login
  */

  public function login($params = [])
  {
    $this->view('auth/signin', $params);
  }

  /*
  ** This function will check the db to see if the email or username is taken
  ** if they are available, the users will be temporarily added as a user until
  ** email validation.
  */

  private function register_user($email, $username, $password)
  {

  }

  /*
  ** This function will either render a view or it will register a user
  ** from the ajax request
  */

  public function signup($params = [])
  {
    if (filter_has_var(INPUT_POST, 'email')
     && filter_has_var(INPUT_POST, 'username')
     && filter_has_var(INPUT_POST, 'password'))
    {
      echo json_encode(array('name' => 'andre'));
    }
    else
      $this->view('auth/signup', $params);
  }
}
