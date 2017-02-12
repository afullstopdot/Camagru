<html lang="en">

  <head>
    <title>Camagru - Reset</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/css/camagru.css">
  </head>

  <body style="background-image: url('<?php 
                                        if (($ran = rand(0, 2)) == 0) { 
                                          echo SITE_URL . "/imgs/0.jpg"; 
                                        } else if ($ran == 1) { 
                                          echo SITE_URL . "/imgs/2.jpg"; 
                                        } else { 
                                          echo SITE_URL . "/imgs/1.jpg"; 
                                        } 
                                    ?>');">

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="/Camagru/public/home" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="/Camagru/public/auth/logout">Log out</a></li>'; } else { echo '<li><a href="/Camagru/public/auth/login">Log in</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>

    <form id="reset" name="reset" action="/Camagru/public/auth/reset" method="POST">
      <?php if (isset($data['resp'])) { 
          echo '
            <div class="container">
              <h3 class="info-text" style="color: gold; text-align: center;">Last step, choose a new password!</h3>
              <label><b class="p-text" id="b-password" style="color: #14D385;">New password</b></label>
              <p id="err-password" style="color: red; display: none; font-style: bold;">:</p>
              <input id="password1" type="password" placeholder="8 characters minimum" name="password1" required>
              <label><b class="p-text" id="b-password" style="color: #14D385;">Confirm password</b></label>
              <p id="err-password" style="color: red; display: none; font-style: bold;">:</p>
              <input id="password2" type="password" placeholder="8 characters minimum" name="password2" required>
              <button id="reset-button" type="submit" style="background-color: #333; font-family: "Architects Daughter", cursive;">Reset account!</button>
            </div>
          '; 
        } else { 
          echo '
            <div class="container">
              <h3 class="info-text" style="color: gold; text-align: center;">Enter a valid account email to reset!</h3>
              <label><b class="p-text" id="b-email" style="color: #14D385;">E-mail</b></label>
              <p id="err-email" style="color: red; display: none; font-style: bold;">:</p>
              <input id="email" type="email" placeholder="placeholder@domain.com" name="email" required>
              <button id="reset-button" type="submit" style="background-color: #333; font-family: "Architects Daughter", cursive;">Reset account!</button>
            </div>
          '; 
        }
      ?>
    </form>

    <footer><p style="text-align: center; color: white;">developed by afullstopdot</p></footer>

    <script src="<?php echo SITE_URL; ?>/js/camagru.js"></script>

  </body>

</html>

<?php if (isset($_SESSION['flash'])) { unset($_SESSION['flash']); } ?>
