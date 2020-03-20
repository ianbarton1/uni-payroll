<?php
  //error reporting set $display_oopsies to true to enable error logging
  session_start();
  $_SESSION["display_oopsies"] = false;
  require('paths.php');
  include('error_enable.php');
  include('functions.php');
  //loads data from file into a session variable array with the name employee_data.
if (load_data($paths_data.'employees-mod.json', 'employee_data', true) == false) {
echo "Error: JSON file not found!";
exit();
}
  //Save data *keep around for saving file
  //save_data('/home/ianbarton1990/phpdata/assignmentdata/employees-mod.json', 'employee_data');

?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = "Payroll System - View Employees Page";
    $description = "Main page with employee view";
    include("head.php");
  ?>
  <body>
    <header>
      <nav>
        <div class="container-fluid">
          <div class="d-flex flex-row justify-content-center">
            <h1 id=""> Payroll System</h1>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <div class="container">
        <div class="d-flex justify-content-center">
          <form action=index.php method="get">
            <input name="search_query" placeholder="Search..." type="search" value='<?php echo $_GET["search_query"]; ?>' autofocus>
            <input type="submit">
          </form>
        </div>
      </div>
      <div class="container">
        <div class="d-flex justify-content-center">
          <?php
          //shows table of employees see functions file for documentation
            show_table('employee_data', 'style_employee_table', 'employee_table',
            'id,nationalinsurance,lastname,firstname,jobtitle,department,salary,*tax,currency,*details',
            'ID ,NI ,Last Name ,First Name , Job Title, Department, Salary, Net Pay, , Details'
            ,$_GET["search_query"]);
          ?>
        </div>
      </div>


    </main>
    <?php
      include("footer-inc.php")
    ?>
  </body>
</html>
