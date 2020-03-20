<?php
  session_start();
  require('paths.php');
  include('error_enable.php');
  include('functions.php');
  //loads data from file into a session variable array with the name employee_data.
  load_data($paths_data.'employees-mod.json', 'employee_data', true);
  //Save data *keep around for saving file
  //save_data('/home/ianbarton1990/phpdata/assignmentdata/employees-mod.json', 'employee_data');
  foreach ($_SESSION["employee_data"] as $data_id => $key)
  if ($key["id"]==$_GET["id"]) {
    $data_key = $data_id;
    break;
  }

  if (!isset($data_key)) {
    header("Location: employee_not_found.php");
  }

  if ($_FILES["display_pic"]["type"] == 'image/jpeg' || $_FILES["display_pic"]["type"] == 'image/png') {
   $file_check = true;
 } else {
   $file_check = false;
}
if (isset($_FILES['display_pic']) && $file_check == true) {
  imagepng(imagescale(imagecreatefromstring(file_get_contents($_FILES['display_pic']['tmp_name'])), 370, 370),$paths_upload.$_GET['id'].".png");
  unset($_FILES['display_pic']);
  header("Location: employee.php?id=".$_GET["id"]);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <?php
    $title = "Payroll System - Update picture for ".$_GET["id"];
    $description = "Employee Details";
    include("head.php");
  ?>
  <body>
    <header>
      <nav>
        <div class="container-fluid">
          <div class="d-flex flex-row justify-content-center">
            <h1 id=""> Update picture for <?php echo $_SESSION["employee_data"][$data_id]["lastname"].", ".$_SESSION["employee_data"][$data_id]["firstname"]." {Employee ID: ".$_GET["id"]."}"?></h1>
          </div>
        </div>
      </nav>
    </header>
    <main>

    <div class='container'>
      <div class="d-flex flex-row justify-content-center">
        <form action = '<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"];?>' method='post' enctype='multipart/form-data'>
          <input type='file' name='display_pic' id='display_pic'>
          <input type='submit' value='Upload Picture'>
        </form>
      </div>
    </div>

    </main>
    <?php
      include("footer-inc.php")
    ?>
  </body>
</html>
