<?php
  //error reporting set $display_oopsies to true to enable error logging
  include('error_enable.php');
  require('paths.php');
?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = "Payroll System - Employee Not Found ".$_GET["id"];
    $description = "Error page`";
    include("head.php");
  ?>
  <body>
    <header>
      <nav>
        <div class="container-fluid">
          <div class="d-flex flex-row justify-content-center">
            <h1>Error: Employee not found</h1>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <div class="container">
        <div class="d-flex flex-row justify-content-center">
        <p>Employee was not found in file, please go back and try again</p>
        </div>
      </div>
    </main>
    <?php
      include("footer-inc.php")
    ?>
  </body>
</html>
