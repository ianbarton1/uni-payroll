<?php
  session_start();
  include('paths.php');
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

?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = "Payroll System - Details for employee ".$_GET["id"];
    $description = "Employee Details";
    include("head.php");
  ?>
  <body>
    <header>
      <nav>
      </nav>
    </header>
    <main>
      <div class="container bg_2">
        <?php
        show_picture_box($data_id
      ,'firstname,lastname,nationalinsurance,dob'
      ,'First Name,Surname,National Insurance,Date of Birth');?>
      <?php
      show_employee_data($data_id
       ,'homeaddress,email,homeemail,employmentstart,employmentend,*employmentlength,jobtitle,previousroles,otherroles'
       ,'Address,Work E-Mail,Personal E-Mail,Started Employment,Ended Employment,Length of Service,Current Role,Previous Roles,Other Roles');
      ?>
      </div>
    </main>
    <?php
      include("footer-inc.php");
    ?>
  </body>
</html>
