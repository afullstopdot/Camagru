<?php

class auth extends Controller
{
  public function googlesignup($params = [])
  {
    $this->view('home/index', $params);
  }

  public function fourtytwosignup($params = [])
  {
    $this->view('home/index', $params);
  }

  public function reset($params = [])
  {
    $this->view('auth/reset', $params);
  }

  public function login($params = [])
  {
    $this->view('auth/signin', $params);
  }

  private function register_user($email, $username, $password)
  {

  }

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
