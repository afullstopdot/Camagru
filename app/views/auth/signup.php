<!DOCTYPE html>
<?php header('Content-Type: text/html'); ?>
<html lang="en">

  <head>
    <title>Camagru - Create</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/css/camagru.css">
  </head>

  <body>

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="/Camagru/public/home" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="/Camagru/public/auth/logout">Log out</a></li>'; } else { echo '<li><a href="/Camagru/public/auth/login">Log in</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>

    <form id="signup" name="signup" action="/Camagru/public/auth/signup" method="POST">
      <div class="container">
        <h3 style="color: gold; text-align: center;">Sign Up to create, share & like pics!</h3>
        <label><b id="b-email" style="color: #14D385;">E-mail</b></label>
        <p id="err-email" style="color: red; display: none; font-style: bold;">:</p>
        <input id="email" type="email" placeholder="placeholder@domain.com" name="email" required>

        <label><b id="b-username" style="color: #14D385;">Username</b></label>
        <p id="err-username" style="color: red; display: none; font-style: bold;">:</p>
        <input id="username" type="text" placeholder="harambe" name="username" required>

        <label><b id="b-password" style="color: #14D385;">Password</b></label>
        <p id="err-password" style="color: red; display: none; font-style: bold;">:</p>
        <input id="password" type="password" placeholder="8 characters minimum" name="password" required>

        <button id="signup-button" type="submit" style="background-color: #333; font-family: 'Architects Daughter', cursive;">Sign me up!</button>
        <div id="oauth">
          <a href="/Camagru/public/auth/facebooksignup"><button id="facebook" style="font-family: 'Architects Daughter', cursive;" type="button">Facebook</button></a>
          <a href="/Camagru/public/auth/googlesignup"><button id="google" style="font-family: 'Architects Daughter', cursive;" type="button">Google+</button></a>
          <a href="/Camagru/public/auth/fourtytwosignup"><button id="fourty" style="font-family: 'Architects Daughter', cursive;" type="button">42</button></a>
          <a href="/Camagru/public/auth/twittersignup"><button id="twitter" style="font-family: 'Architects Daughter', cursive;" type="button">Twitter</button></a>
        </div>
      </div>
    </form>

    <footer>

      <p style="text-align: center; color: #333;">developed by afullstopdot</p>

    </footer>

    <script src="<?php echo SITE_URL; ?>/js/camagru.js"></script>

  </body>

</html>
