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
    $response = $this->model('user')->reg_response($email, $username);

    /*
    ** function reg_response will return an array which will dictate below
    ** whether to create a new account or not. regardless the response will
    ** be returned so the javascript can update the user with how the registration
    ** went
    */

    if ($response['username'] === 'OK' && $response['email'] === 'OK')
    {
      //add user to temp_users, send e-mail verification
      $result = $this->model('user')->create_temp_account($email, $username, $password);
      if ($result === true)
        echo json_encode(['error' => 'no errors.']);
      else
        echo json_encode($result);
    }

    // echo json_encode ($response);
  }

  /*
  ** The verification email sent will be dealt with here
  */

  public function verify($params = [])
  {
    // make this a controller because for some reason only 1 param is being passed
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
      $this->register_user(
        trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)),
        trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)),
        trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING))
      );
    }
    else
      $this->view('auth/signup', $params);
  }
}
