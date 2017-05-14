<html lang="en">
  <?php if (file_exists(ROOT_DIR . '/app/views/partials/header.php')) { $title = "Home"; require_once ROOT_DIR . '/app/views/partials/header.php'; } ?>

    <header>
      <center id="hash"></center>
      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="<?php echo SITE_URL; ?>" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php 
          if (isset($_SESSION['user'])) { 
            echo '<li><a href="' . SITE_URL . '/auth/logout">Log out</a></li>'; 
          } 
          else { 
            echo '<li><a href="' . SITE_URL . '/auth/login">Log in</a></li>'; 
          } 
        
          if (isset($_SESSION['user'])) { 
            echo '<li><a href="' . SITE_URL . '/profile/home">Profile</a></li>'; 
          } 
          else { 
            echo '<li><a href="' . SITE_URL . '/auth/signup">Create Account</a></li>'; 
          } 
        ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>
    </header>

    <?php if (file_exists(ROOT_DIR . '/app/views/flash/flash.php')) { require_once ROOT_DIR . '/app/views/flash/flash.php'; } ?>

    <div id="loading-div" class="loader"><p style="color: white;">Loading</p></div>

    <?php
      if (!empty($data['uploads'])) 
      {
        $likes_count = 0;
        $comments_count = 0;
        echo '<div class="container-fluid">';
          foreach ($data['uploads'] as $value) {

            /*
            ** Calculate # of likes and comments
            */

            foreach ($data['likes'] as $likes) {
              if ($value['image_id'] === $likes['image_id']) {
                $likes_count++;
              }
            }

            foreach ($data['comments'] as $comments) {
              if ($value['image_id'] === $comments['image_id']) {
                $comments_count++;
              }
            }

            /*
            ** Display all images, comments, likes
            */

            echo '
              <div id="' . $value['image_id'] . '" class="image-card">
                <img id="' . $value['image_id'] . '" ondblclick="like(this.id)" src="' . $value['img_path'] . '" alt="' . $value['image_id'] . '" style="width:100%">
                <button class="accordion">
                  <div class="profile-picture">
                    <img src="' . $value['picture'] . '" alt="' . $value['image_id'] . '" width="96" height="96">
                    ' . $value['username'] . '
                  </div>
                  <div class="heart">
                    <p><b id="' . $value['image_id'] . ' like-count">' . $likes_count . '</b> likes <b id="comment-count">' . $comments_count . '</b> comments</p>
                  </div>
                </button>
                <div class="panel">
                  <b>
                    <span class="like-button" id="' . $value['image_id'] . '" onclick="like(this.id)">&hearts; Like</span>
                  </b>
                  <form id="comment-form" action="home/comment/' . $value['image_id'] . '" name="' . $value['image_id'] . '-form' . '" method="POST" role="form">
                    <textarea name="data" class="comment-area" placeholder="LOL great pic!" rows="3"></textarea>
                    <button type="submit" id="' . $value['image_id'] . '" name="comment-submit" class="comment-submit-button">Comment</button>
                  </form>
                  <div id="' . $value['image_id'] . '-comment-panel">';
                    foreach ($data['comments'] as $comments) {
                      if ($comments['image_id'] === $value['image_id']) {
                        echo '<p><strong style="color: white;">' . $comments['username'] . '</strong> ' . $comments['comment'] . '</p>';
                      }
                    }
            echo  '<p>&sharp;</p>
                  </div>
                </div>
              </div>
            ';

            $likes_count = 0;
            $comments_count = 0;
          }
        echo '</div>';
        echo '<div class="load-more">Load More</div>';
      }
      else {
        echo '
          <div class="image-card">
            <p style="color: red; text-align: center; font-size: 22px;">No uploads</p>
          </div>
        ';
      }
    ?>

    <div id="dialog-modal" class="modal">
      <div id="header-modal" class="modal-header"></div>
      <div id="content-modal" class="modal-content">
        <span id="close-modal" class="modal-close">&times;</span>
      </div>
    </div>

    <?php if (file_exists(ROOT_DIR . '/app/views/partials/footer.php')) { require_once ROOT_DIR . '/app/views/partials/footer.php'; } ?>
</html>