<?php

/*
**
*/

class App
{
  protected $controller = 'home';
  protected $method = 'index';
  protected $params = [];

  public function __construct()
  {
    $url = $this->parseUrl();

    if (file_exists('../app/controllers/' . $url['controller'] . '.php'))
    {
      $this->controller = $url['controller'];
    }

    require_once '../app/controllers/' . $this->controller . '.php';

    $this->controller = new $this->controller;

    if (isset($url['method']))
    {
      if (method_exists($this->controller, $url['method']))
      {
        $this->method = $url['method'];
      }
    }

    $this->params = $url['params'] ? $url['params'] : [];

    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  /*
  ** parseUrl will extract the controller, method and params
  ** from the $_GET var the rewrite from the .htaccess returned
  */

  protected function parseUrl()
  {
    if (isset($_GET['url']))
    {
      $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
      return array(
        'controller' => $url[0],
        'method' => $url[1],
        'params' => array_slice($url, 2)
      );
    }
  }
}
