<?php

class auth extends Controller
{
  public function reset()
  {
    $this->view('auth/reset', $params);
  }

  public function login()
  {
    $this->view('auth/signin', $params);
  }

  public function signup($params = [])
  {
    $this->view('auth/signup', $params);
  }
}
