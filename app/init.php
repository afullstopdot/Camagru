<?php

/*
** Require all dependincies (if dependencies dont exist exit before crash)
** to create new instance, also define values and set the timezone etc
*/

session_start();
// session_destroy();die(); //for debugging

require_once 'core/App.php';
require_once 'controllers/Controller.php';
require_once 'config/database.php';

/*
** Create pdo object and assign it to the controller class if it doesnt exist,
** thus might be pointless because the state is never constant so a new pdo
** object will be created because the controller pdo will not be set.
** Create sql db and tables that this app depends on if they dont exist yet
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

/*
** Set the timezone for johannesburg
*/

date_default_timezone_set('Africa/Johannesburg');

/*
** SITE_URL is the host
** Vars prefixed with SLACK are the oauth api endpoints used in auth.php
*/

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__) . '/public')));
define('SLACK_AUTH', 'https://slack.com/oauth/authorize?');
define('SLACK_ACCESS', 'https://slack.com/api/oauth.access');
define('SLACK_PROFILE', 'https://slack.com/api/users.profile.get');
