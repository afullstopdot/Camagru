<?php

class home extends Controller
{
  /*
  ** renders view for home, should display every image posted to date
  */

  public function index($params = [])
  {
    $this->view('home/index', $params);
  }
}
