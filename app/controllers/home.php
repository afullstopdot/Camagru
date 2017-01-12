<?php

class home extends Controller
{
  public function index($params)
  {
    $this->view('home/index', $params);
  }
}
