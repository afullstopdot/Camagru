<?php

/*
** This class is onl accessible to admin
** Should be used to do stuff like, removing tables
** clearing tables. Only used for development.
*/

class dev extends Controller
{
  public function index()
  {
    if (isset($_SESSION['user']))
    {
      /*if ($this->auth->admin() == true)
      {
        // display page with super user content
      }*/
    }
    else
    {
      $_SESSION['flash'] = ['message' => 'Oops, only admin allowed.'];
      $this->view('home/index');
      if (isset($_SESSION['flash']))
        unset($_SESSION['flash']);
    }
  }
}
