<?php

try
{
  /*
  ** Create database
  */

  $db->query('CREATE DATABASE IF NOT EXISTS db_camagru');

  /*
  ** Select database
  */

  $db->query('USE db_camagru');

  /*
  ** Create permanent user table - post verification
  */

  $db->query(
    'CREATE TABLE IF NOT EXISTS users (
      user_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      email varchar(250) NOT NULL,
      username varchar(16) NOT NULL,
      password text,
      admin int(11) UNSIGNED,
      picture text,
      joined datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      reset text
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ');

  /*
  ** Create temporary user table - pre verification
  */

  $db->query(
    'CREATE TABLE IF NOT EXISTS unverified_users (
      user_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      email varchar(250) NOT NULL,
      username varchar(16) NOT NULL,
      password text,
      joined datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      verification text
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ');

  /*
  ** Create a table for image uploads
  */

  $db->query(
    'CREATE TABLE IF NOT EXISTS uploads (
      image_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_id int(11) UNSIGNED,
      img_path text,
      upload_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ');

  /*
  ** Create a tbale for comments
  */

  $db->query(
    'CREATE TABLE IF NOT EXISTS comments (
      comment_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      image_id int(11) UNSIGNED,
      user_id text,
      comment text,
      comment_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ');

  /*
  ** Create a table for likes
  */

  $db->query(
    'CREATE TABLE IF NOT EXISTS likes (
      like_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      image_id int(11) UNSIGNED,
      user_id int(11) UNSIGNED,
      like_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
  ');
}
catch (PDOException $e)
{
  die('<h1 style="text-align: center;">Something terrible has happend with the db</h2>');
  //die('FATAL ERROR: ' . $e->getMessage()); //for debugging
}