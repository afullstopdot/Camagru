
<html lang="en">

  <head>
    <title>Camagru - Edit</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial scale=1">
    <link href="https://fonts.googleapis.com/css?family=Architects+Daughter|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/css/camagru-profile.css">
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
      <center id="hash"></center>
      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="/Camagru/public/home" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php 
          if (isset($_SESSION['user'])) { 
            echo '<li><a href="/Camagru/public/auth/logout">Log out</a></li>'; 
          } 
          else { 
            echo '<li><a href="/Camagru/public/auth/login">Log in</a></li>'; 
          }
        ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>

    </header>

    <div id="loading-div" class="loader"><p style="color: white;">Loading</p></div>

    <form id="image-form">
      <div class="left">
        <input type="file" id="file" name="image">
        <label id="file-label" for="file">Upload a image</label>
        <div id="preview">
          <img id="image-preview">
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
            /*
            ** If you want to change assets, go to init file
            */
            foreach (ASSET_NAME as $key => $value) {
              echo '<img src="'. ASSET_PATH . $value . '">';
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

    <script src="<?php echo SITE_URL; ?>/js/camagru-profile.js"></script>

  </body>

</html>