<?php

class App
{
  protected $controller = 'home';
  protected $method = 'index';
  protected $params = [];

  /*
  ** Every request creates a new instance of this app, the constructor if applicable
  ** adjusts the controller, method and view depending on whether the current
  ** uri has legitimate values (controller-method)
  */

  public function __construct()
  {
    $url = $this->parseUrl();

    if (file_exists(ROOT_DIR . '/app/controllers/' . $url['controller'] . '.php'))
    {
      $this->controller = $url['controller'];
    }

    require_once ROOT_DIR . '/app/controllers/' . $this->controller . '.php';

    $this->controller = new $this->controller;

    if (isset($url['method']))
    {
      if (method_exists($this->controller, $url['method']))
      {
        $this->method = $url['method'];
      }
    }

    $this->params = $url['params'] ? array_values($url['params']) : [];

    /*
    ** By this point we know what controller-method should be called, be it the
    ** default defined by Controller attributes or the requested uri.
    ** note: for some weird reason call_user_func_array never worked.
    */

    call_user_func([$this->controller, $this->method], $this->params);

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
        'controller' => isset($url[0]) ? $url[0] : 'home',
        'method' => isset($url[1]) ? $url[1] : 'index',
        'params' => array_slice($url, 2)
      );
    }
  }
}
