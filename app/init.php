<?php

/*
** Require all dependincies to create new instance
** define values
** set the timezone etc
*/

require_once 'core/App.php';
require_once 'controllers/Controller.php';
require_once 'config/database.php';

/*
** Create pdo object, database, tables etc only if it hasnt been done.
*/

try
{
  if (Controller::getDB() === NULL)
  {
    $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    Controller::setDB($db);
  }
  else
    $db = Controller::getDB();
  require_once ('config/setup.php');
}
catch (PDOException $e)
{
  echo 'Camagru Internal Server Error: ' . $e->getMessage();
  exit();
}

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__) . '/public')));
