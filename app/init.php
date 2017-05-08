<?php

/*
** Require all dependincies (if dependencies dont exist exit before crash)
** to create new instance, also define values and set the timezone etc
*/

session_start();

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
  echo 'Camagru Internal Server Error: ';
  // echo 'Camagru Internal Server Error: ' . $e->getMessage();
  exit();
}

/*
** Set the timezone for johannesburg
*/

date_default_timezone_set('Africa/Johannesburg');

/*
** Admin email for errors
*/

define('ADMIN_EMAIL', 'andreantoniomarques19@gmail.com');

/*
** SITE_URL is the host and ROOT_DIR is document root
** Vars prefixed with SLACK are the oauth api endpoints used in auth.php
*/

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__) . '/public')));
define('SITE_HOST', 'http://' . $_SERVER['HTTP_HOST']);

/*
** These urls will be used for slacks oauth 2.0 web flow
*/

define('SLACK_AUTH', 'https://slack.com/oauth/authorize?');
define('SLACK_ACCESS', 'https://slack.com/api/oauth.access');
define('SLACK_PROFILE', 'https://slack.com/api/users.profile.get');

/*
** These urls will be used for githubs oauth 2.0 web flow
*/

define('GITHUB_AUTH', 'https://github.com/login/oauth/authorize?');
define('GITHUB_ACCESS', 'https://github.com/login/oauth/access_token');
define('GITHUB_PROFILE', 'https://api.github.com/user');
define('GITHUB_EMAIL', 'https://api.github.com/user/emails');

/*
** These urls will be used for googles oauth 2.0 web flow
*/

define('GOOGLE_AUTH', 'https://accounts.google.com/o/oauth2/v2/auth?');
define('GOOGLE_ACCESS', 'https://www.googleapis.com/oauth2/v4/token');
define('GOOGLE_PROFILE', 'https://www.googleapis.com/plus/v1/people/me');

/*
** These urls will be used for 42s oauth 2.0 web flow
*/

define('E42_AUTH', 'https://api.intra.42.fr/oauth/authorize?');
define('E42_ACCESS', 'https://api.intra.42.fr/oauth/token');
define('E42_PROFILE', 'https://api.intra.42.fr/v2/me');

/*
** Asset path for url (superimposable images location)
** Asset names
*/

define('ASSET_PATH', '/Camagru/app/views/assets/');
define('ASSET_NAME', [
  's-1.png',
  's-2.png',
  's-3.png',
  's-4.png',
  's-5.png',
  's-6.png'
]);

/*
** Directory where all images will be saved
*/

define('UPLOAD_DIR', '/Camagru/app/views/uploads/');