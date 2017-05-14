<html lang="en">

  <?php if (file_exists(ROOT_DIR . '/app/views/partials/header.php')) { $title = "Reset Password"; require_once ROOT_DIR . '/app/views/partials/header.php'; } ?>

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="<?php echo SITE_URL; ?>" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="' . SITE_URL . '/auth/logout">Log out</a></li>'; } else { echo '<li><a href="' . SITE_URL . '/auth/login">Log in</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>

    <?php if (file_exists(ROOT_DIR . '/app/views/flash/flash.php')) { require_once ROOT_DIR . '/app/views/flash/flash.php'; } ?>

    <form id="reset" name="reset" action="<?php echo SITE_URL; ?>/auth/reset" method="POST">
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
              <button id="reset-button" type="submit">Reset account!</button>
            </div>
          '; 
        } else { 
          echo '
            <div class="container">
              <h3 class="info-text" style="color: gold; text-align: center;">Enter a valid account email to reset!</h3>
              <label><b class="p-text" id="b-email" style="color: #14D385;">E-mail</b></label>
              <p id="err-email" style="color: red; display: none; font-style: bold;">:</p>
              <input id="email" type="email" placeholder="placeholder@domain.com" name="email" required>
              <button id="reset-button" type="submit">Reset account!</button>
            </div>
          '; 
        }
      ?>
    </form>

    <?php if (file_exists(ROOT_DIR . '/app/views/partials/footer.php')) { require_once ROOT_DIR . '/app/views/partials/footer.php'; } ?>

</html>