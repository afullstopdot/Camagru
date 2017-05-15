
<html lang="en">

  <head>
    <title>Camagru - Edit</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/public/css/camagru-profile.css">
  </head>

  <body style="background-image: url('<?php 
      if (($ran = rand(0, 2)) == 0) { 
        echo SITE_URL . "/public/imgs/0.jpg"; 
      } 
      else if ($ran == 1) { 
        echo SITE_URL . "/public/imgs/2.jpg"; 
      } else { 
        echo SITE_URL . "/public/imgs/1.jpg"; 
      } 
    ?>');">

    <header>
      <center id="hash"></center>
      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="<?php echo SITE_URL; ?>" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php 
          if (isset($_SESSION['user'])) { 
            echo '
              <li><a href="' . SITE_URL . '/auth/logout">Log out</a></li>
              <li class="toggle"><a href="#">Toggle</a></li>
            '; 
          } 
          else { 
            echo '<li><a href="' . SITE_URL . '/auth/login">Log in</a></li>'; 
          }
        ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>
    </header>

    <?php if (file_exists(ROOT_DIR . '/app/views/flash/flash.php')) { require_once ROOT_DIR . '/app/views/flash/flash.php'; } ?>

    <div id="loading-div" class="loader"><p style="color: white;">Loading</p></div>

    <form id="image-form">
      <div class="left">
        <input type="file" id="file" name="image">
        <label id="file-label" for="file">Upload a image</label>
        <div id="preview">
          <img id="image-preview">
          <video id="cam-preview"></video>
          <canvas id="cam-canvas"></canvas>
        </div>
        <img id="image-png" src="s-1.png">
        <button id="submit">Snap!</button>
      </div>
      <div class="headlines">
        <p>SELECT IMAGE BELOW</p>
        <hr>
      </div>
      <div id="option-list" class="options">
          <?php
            
            $asset =  [
              's-1.png',
              's-2.png',
              's-3.png',
              's-4.png',
              's-5.png',
              's-6.png'
            ];

            /*
            ** If you want to change assets, go to init file
            */
            foreach ($asset as $key => $value) {
              echo '<img src="'. SITE_URL . ASSET_PATH . $value . '">';
            }
          ?>
      </div>
      <div class="headlines">
        <p>THUMBNAILS</p>
        <hr>
      </div>
      <div class="thumbnails">
      </div>
    </form>

    <div class="img-modal">
      <h2><?php if (isset($data['username'])) { echo $data['username']; } else { echo 'N/A'; }?><span id="img-close">×</span></h2>
      <hr>
      <img src="">
      <button id="del-img">Delete Post</button>
      <div class="likes-count">?</div>
    </div>

    <div id="dialog-modal" class="modal">
      <div id="header-modal" class="modal-header"></div>
      <div id="content-modal" class="modal-content">
        <span id="close-modal" class="modal-close">&times;</span>
      </div>
    </div>

      <footer><p style="text-align: center; color: white;">developed by afullstopdot</p></footer>
    <script src="<?php echo SITE_URL; ?>/public/js/camagru-profile.js"></script>
  </body>

<?php if (isset($_SESSION['flash'])) { unset($_SESSION['flash']); } ?>

</html>