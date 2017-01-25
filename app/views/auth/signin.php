<html lang="en">

  <head>
    <title>Camagru - Log In</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/css/camagru.css">
  </head>

  <body>

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="/Camagru/public/home" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <li><a href="/Camagru/public/auth/reset">Reset Account</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="/Camagru/public/auth/logout">Log out</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>


    <?php if (file_exists('../app/views/flash/flash.php')) { require_once '../app/views/flash/flash.php'; } else { echo '<h1>Could not load flash</h1>'; } ?>

    <form id="signin" name="signin" action="/Camagru/public/auth/login" method="POST">
      <div class="container">
        <h3 class="info-text" style="color: gold; text-align: center;">Log in to create, share & like pics!</h3>
        <label><b class="p-text" id="b-email" style="color: #14D385;">E-mail</b></label>
        <p id="err-email" style="color: red; display: none; font-style: bold;">:</p>
        <input id="email" type="email" placeholder="placeholder@domain.com" name="email" required>

        <label><b class="p-text" id="b-password" style="color: #14D385;">Password</b></label>
        <p id="err-password" style="color: red; display: none; font-style: bold;">:</p>
        <input id="password" type="password" placeholder="your secret password" name="password" required>

        <button id="signup-button" type="submit" style="background-color: #333; font-family: 'Architects Daughter', cursive;">Log me in !</button>
        <fieldset id="leglin"><legend align="center"><p style="color: white;">or</p></legend></fieldset>
        <div id="oauth">
          <a href="/Camagru/public/auth/slack/signin"><button id="slack" style="font-family: 'Architects Daughter', cursive;" type="button">Slack</button></a>
          <a href="/Camagru/public/auth/google/signin"><button id="google" style="font-family: 'Architects Daughter', cursive;" type="button">Google+</button></a>
          <a href="/Camagru/public/auth/fourtytwo/signin"><button id="fourty" style="font-family: 'Architects Daughter', cursive;" type="button">42</button></a>
          <a href="/Camagru/public/auth/github/signin"><button id="github" style="font-family: 'Architects Daughter', cursive; color: black;" type="button">Github</button></a>
        </div>
      </div>
    </form>


    <footer><p style="text-align: center; color: white;">developed by afullstopdot</p></footer>

    <script src="<?php echo SITE_URL; ?>/js/camagru.js"></script>
  </body>

</html>
