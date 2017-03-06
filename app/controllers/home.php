<?php

class home extends Controller
{
  /*
  ** renders view for home, should display every image posted to date
  */

  public function index($params = [])
  {
  	$data = $this->model('gallery')->getUploads();

  	if ($data === false)
  	{
	   	$this->view('home/index', $params);
  	}
    $this->view('home/index', $data);
  }
}

/*
** Use accordion for comments
** https://www.w3schools.com/howto/howto_js_accordion.asp
*/