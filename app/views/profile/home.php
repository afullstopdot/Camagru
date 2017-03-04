
<html lang="en">

  <head>
    <title>Camagru - Edit</title>
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

    <?php if (file_exists('../app/views/flash/flash.php')) { require_once '../app/views/flash/flash.php'; } else { echo '<h1>Could not load flash</h1>'; } ?>

    <footer><p style="text-align: center; color: white;">developed by afullstopdot</p></footer>

    <script src="<?php echo SITE_URL; ?>/js/camagru.js"></script>

  </body>

</html>

<?php if (isset($_SESSION['flash'])) { unset($_SESSION['flash']); } ?>
