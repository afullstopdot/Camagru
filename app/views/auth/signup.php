<html lang="en">

  <?php if (file_exists(ROOT_DIR . '/app/views/partials/header.php')) { $title = "Home"; require_once ROOT_DIR . '/app/views/partials/header.php'; } ?>

    <header>

      <ul class="topnav" id="myTopnav">
        <li><a class="active" href="<?php echo SITE_URL; ?>" style="font-family: 'Architects Daughter', cursive;">Camagru</a></li>
        <?php if (isset($_SESSION['user'])) { echo '<li><a href="' . SITE_URL . '/auth/logout">Log out</a></li>'; } else { echo '<li><a href="' . SITE_URL . '/auth/login">Log in</a></li>'; } ?>
        <li class="icon"><a href="javascript:void(0);" style="font-size:15px;" onclick="open_close()">☰</a></li>
      </ul>


    </header>

    <?php if (file_exists(ROOT_DIR . '/app/views/flash/flash.php')) { require_once ROOT_DIR . '/app/views/flash/flash.php'; } ?>

    <form id="signup" name="signup" action="<?php echo SITE_URL; ?>/auth/signup" method="POST">
      <div class="container">
        <h3 class="info-text" style="color: gold; text-align: center;">Sign Up to create, share & like pics!</h3>
        <label><b class="p-text" id="b-email" style="color: #14D385;">E-mail</b></label>
        <p id="err-email" style="color: red; display: none; font-style: bold;">:</p>
        <input id="email" type="email" placeholder="placeholder@domain.com" name="email" required>

        <label><b class="p-text" id="b-username" style="color: #14D385;">Username</b></label>
        <p id="err-username" style="color: red; display: none; font-style: bold;">:</p>
        <input id="username" type="text" placeholder="harambe" name="username" required>

        <label><b class="p-text" id="b-password" style="color: #14D385;">Password</b></label>
        <p id="err-password" style="color: red; display: none; font-style: bold;">:</p>
        <input id="password" type="password" placeholder="8 characters minimum" name="password" required>

        <button id="signup-button" type="submit" style="background-color: #333; font-family: 'Architects Daughter', cursive;">Sign me up !</button>
        <fieldset id="leglin"><legend align="center"><p style="color: white;">or</p></legend></fieldset>
        <div id="oauth">
          <a href="<?php echo SITE_URL; ?>/auth/slack/signup"><button id="slack" style="font-family: 'Architects Daughter', cursive;" type="button">Slack</button></a>
          <a href="<?php echo SITE_URL; ?>/auth/google/signup"><button id="google" style="font-family: 'Architects Daughter', cursive;" type="button">Google+</button></a>
          <a href="<?php echo SITE_URL; ?>/auth/fourtytwo/signup"><button id="fourty" style="font-family: 'Architects Daughter', cursive;" type="button">42</button></a>
          <a href="<?php echo SITE_URL; ?>/auth/github/signup"><button id="github" style="font-family: 'Architects Daughter', cursive; color: black;" type="button">Github</button></a>
        </div>
      </div>
    </form>

  <?php if (file_exists(ROOT_DIR . '/app/views/partials/footer.php')) { require_once ROOT_DIR . '/app/views/partials/footer.php'; } ?>
</html>