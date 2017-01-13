<!DOCTYPE html>
<?php header('Content-Type: text/html'); ?>
<html lang="en">

  <head>
    <title>Camagru - Create</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <style>
      body
      {
        margin:0;
        height: 100%;
        background-image: url('http://wallpapers-and-backgrounds.net/wp-content/uploads/2016/01/photography-1080p-background_1_1280x720.jpg');
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        font-family: 'Shadows Into Light', cursive;
      }
      /*Nav bar*/
      ul.topnav
      {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
      }

      ul.topnav li
      {
        float: left;
      }

      ul.topnav li a
      {
        display: inline-block;
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        transition: 0.3s;
        font-size: 17px;
      }

      ul.topnav li a:hover
      {
        background-color: #555;
      }

      ul.topnav li.icon
      {
        display: none;
      }

      @media screen and (max-width:680px) {

        ul.topnav li:not(:first-child)
        {
          display: none;
        }

        ul.topnav li.icon
        {
          float: right;
          display: inline-block;
        }

      }

      @media screen and (max-width:680px) {

        ul.topnav.responsive
        {
          position: relative;
        }

        ul.topnav.responsive li.icon
        {
          position: absolute;
          right: 0;
          top: 0;
        }

        ul.topnav.responsive li
        {
          float: none;
          display: inline;
        }

        ul.topnav.responsive li a
        {
          display: block;
          text-align: left;
        }
      }
      form
      {
        border: 3px solid #f1f1f1;
      }

      input[type=text], input[type=password], input[type=email]
      {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
      }

      button
      {
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
      }

      .cancelbtn
      {
        width: auto;
        padding: 10px 18px;
        background-color: #f44336;
      }

      .container
      {
        padding: 16px;
      }

      span.psw
      {
        float: right;
        padding-top: 16px;
      }

      /* Change styles for span and cancel button on extra small screens */
      @media screen and (max-width: 300px)
      {
        span.psw
        {
          display: block;
          float: none;
        }
        .cancelbtn
        {
           width: 100%;
        }
      }
    </style>
  </head>

  <body>

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="/Camagru/public/home" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="/Camagru/public/auth/logout">Log out</a></li>'; } else { echo '<li><a href="/Camagru/public/auth/login">Log in</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>

    <form action="action_page.php">
      <div class="container">
        <label><b style="color: white;">E-mail</b></label>
        <input type="email" placeholder="placeholder@domain.co" name="email" required>

        <label><b>Password</b></label>
        <input type="password" placeholder="8 characters minimum" name="password" required>

        <button type="submit" style="background-color: #333;">Login</button>
      </div>

      <div class="container" style="background-color:#f1f1f1">
        <a href="/Camagru/public/home" ><button type="button" class="cancelbtn">Cancel</button></a>
        <span class="psw">Forgot <a href="/Camagru/public/auth/reset">password?</a></span>
      </div>
    </form>

    <footer>

      <p style="text-align: center; color: #333;">developed by afullstopdot</p>

    </footer>

    <script>

      function open_close()
      {
        var x = document.getElementById("myTopnav");

        if (x.className === "topnav")
          x.className += " responsive";
        else
          x.className = "topnav";
      }

    </script>

  </body>

</html>
