<?php

class Controller
{
  protected function model($model)
  {
    if (file_exists('../models/' . $model . '.php'))
    {
      require_once '../models/' . $model . '.php';
      return new $model();
    }
    return NULL;
  }

  protected function view($view, $data = [])
  {
    if (file_exists('../views/' . $view . '.php'))
    {
      require_once '../views/' . $view . '.php';
    }
  }
}
