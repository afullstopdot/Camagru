<?php if (isset($_SESSION['flash'])) { echo '<div class="container">'; } ?>

  <?php if (isset($_SESSION['flash']['danger'])) { echo '
    <div class="alert">
      <span class="alertclosebtn">&times;</span>
      <p class="p-text" style="text-align: center;">' . $_SESSION['flash']['danger'] . '</p>
      </div>
  ';} ?>
  <?php if (isset($_SESSION['flash']['success'])) { echo '
    <div class="alert success">
      <span class="alertclosebtn">&times;</span>
      <p class="p-text" style="text-align: center;">' . $_SESSION['flash']['success'] . '</p>
    </div>
  ';} ?>
  <?php if (isset($_SESSION['flash']['info'])) { echo '
    <div class="alert info">
      <span class="alertclosebtn">&times;</span>
      <p class="p-text" style="text-align: center;">' . $_SESSION['flash']['info'] . '</p>
    </div>
  ';} ?>
  <?php if (isset($_SESSION['flash']['warning'])) { echo '
    <div class="alert warning">
      <span class="alertclosebtn">&times;</span>
      <p class="p-text" style="text-align: center;">' . $_SESSION['flash']['warning'] . '</p>
    </div>
  ';} ?>

<?php if (isset($_SESSION['flash'])) { echo '</div>'; } ?>
