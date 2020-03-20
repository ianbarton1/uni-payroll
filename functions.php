<?php
//takes income and company car status and returns tax amount
function tax($income, $company_car, $currency) {
//Just loading the tax tables into an array
$tax_file = file_get_contents('data/tax-tables.json');
$tax_rates = json_decode($tax_file, true);
$total_tax = 0;
if ($currency == 'USD') $income /= 1.28;


//Hard coded super tax variable! :( TOFIX
if ($income > 150000) { $super_tax = true;} else  {$super_tax = false;}

//loop through all tax bands
foreach($tax_rates as $tax_band => $tax) {
  // reset the bonus rate (sets the personal allowance rate to something other than 0%)
  $bonus_rate = 0;
  // we only want to try a tax calculation if income is greater than that tax band's minimum
  if ($income > $tax["minsalary"]) {
        // TOFIX Check to see if any special conditions apply
        // increase the bonus rate by an appropriate amount
        if ($tax_band = 0) {
          if ($company_car == true) {
            $bonus_rate += 0.2;
            //$bonus_rate += $tax_rates[$tax_band+1]["rate"]/100 * ($tax["exceptions"]["Company car"] / 100);
          }
          if ($super_tax == true) {
            $bonus_rate += 0.1;
            //$bonus_rate += $tax_rates[$tax_band+1]["rate"]/100 * ($tax["exceptions"]["Super tax"] / 100);
          }
          //Since bonus rate can't exceed the basic tax rate we run a sanity check
          //and constrain it to be no higher than the next tax rate
          //$bonus_rate = min($bonus_rate, $tax_rates[$tax_band+1]["rate"]/100);
          $bonus_rate = min($bonus_rate, $tax_rates[$tax_band+1]["rate"]/10);
        }
        //a one line calculation for a tax band contribution to final $tax
        // $tax[max] - $ tax[min] calculates the size of the tax bracket
        // $income-$tax.. calculates the amount of income above the start threshold
        // these are run through the minimum function to keep the amount reasonable
        // this amount is then multiplied with the rate and bonus rate and the result is
        // added to the tax amount
    $total_tax += min($tax["maxsalary"]-$tax["minsalary"], $income-$tax["minsalary"]) * ($tax["rate"]/100 + $bonus_rate);
  }
}
//once all tax bands have been considered we return the final answer...
if ($currency == 'USD') $income *= 1.28;
return $total_tax;
}

//loads json file into session unless it's already been loaded with a parameter to force a re-load
function load_data($file_name, $array_name, $force_load) {
  if (isset($_SESSION[$array_name]) == false || $force_load == true) {
    if (file_exists($file_name) == true) {
      $_SESSION[$array_name] = json_decode(file_get_contents($file_name), true);
      return true;
    } else {
      return false;
    }
  }
}


//saves an array of data back to a json file
function save_data($file_name, $array_name) {
  if (isset($_SESSION[$array_name]) == true) {
     file_put_contents($file_name ,json_encode($_SESSION[$array_name]));
  }
}
//table functions
//show table parameters are
//$array_name : the name  of the session array to read from
//$class_sting: name of class to be given to table
//$id_string: name of id to be given to table
//$fields_string: csv string of fields to be displayed in order given, must be exactly as in json file
//special values are "*tax" which returns salary less tax and "*details" which generates links to each Employees
//details page.
//$headings_arg: a csv string containing human friendly headings for the fields each value should correspond
//with the values in $fields_string
function show_table ($array_name, $class_sting, $id_string, $fields_show, $headings_arg, $search_query) {
  //get the csv strings and split them into arrays for processing
  $fields = str_getcsv($fields_show);
  $headings = str_getcsv($headings_arg);
  //start the html table
  echo "<table class='".$class_sting."' id='".$id_string."'>";
  echo "<tr>";
  //loop through all headings and output a html table heading
  foreach ($headings as $value) {
    echo "<th>".$value."</th>";
  }
  echo "</tr>";
  //loop 1: main loop :- Loop through each employee entry
  foreach ($_SESSION[$array_name] as $employee_i => $employee) {
    //prepare the search variables..
    $search_hit = false;
    unset($search_hits);
    //loop 1.1: search loop:- Loop through each entry that is being displayed in table and
    //see if there is a match, recording where matches occur. if the search
    //string is empty we terminate early and pretend we got a search hit and carry on as normal
    foreach ($fields as $search_field) {
      if (empty($search_query)) {
        $search_hit = true;
        break;
      }
      if (stripos($employee[$search_field],$search_query) !== false){
        $search_hit = true;
        $search_hits[] = $search_field;
      }
    }
    //end of loop 1.1
    //this code only runs if the preceeding loop has recorded a search hit
    if ($search_hit == true) {
      echo "<tr>";
      //loop 1.2:- we loop through every field as specified in the $fields_string
      foreach ($fields as $current_field) {
        //hardcoded special values are checked for here
        if ($current_field == "*tax") {
          echo "<td>".number_format($employee["salary"]-tax($employee["salary"], false, $employee["currency"]), 2)."</td>";
        } else if ($current_field == "*details") {
          echo "<td>"."<a href='employee.php?id=".$employee["id"]."'> View</a></td>";
        } else {
            $value = $_SESSION[$array_name][$employee_i][$current_field];
            if (in_array($current_field, $search_hits)) {
            echo "<td class='search_hit'>".$value."</td>";
          } else {
            echo "<td>".$value."</td>";
            }
        }
      }
      //end of loop 1.2
      echo "</tr>";
    }
  }
  //end of loop 1
  echo "</table>";
}

//recursive function to show array entries that are themselves arrays
function show_array_key($level, $value) {
  $level_prefix = "";
  for ($i = 0; $i<$level; $i++) {
    $level_prefix .= " ";
  }
  $level_prefix .= "";

  if (is_array($value) == false) {
    echo $level_prefix.$value."<br>";
  } else {
    foreach ($value as $value_1) {
      show_array_key(($level + 1), $value_1);
    }
  }
}

function show_employee_data($data_id, $fields_show, $human_labels_) {
  $fields = str_getcsv($fields_show);
  $human_labels = str_getcsv($human_labels_);
  foreach ($fields as $field_index => $current_field) {
	  //special fields do maths operations and are otherwise not stored anywhere they are preceded by * character
	  if ($current_field[0] == '*') {
		employee_data_block_1($human_labels[$field_index]);
		echo special_field($data_id, $current_field);
		employee_data_block_2();
		continue;
	  }
    foreach($_SESSION["employee_data"][$data_id] as $key => $value) {
      if ($current_field == $key) {
		employee_data_block_1($human_labels[$field_index]);
        echo show_array_key(0, $value);
		employee_data_block_2();
        break;
      }
    }
  }
}

//employee  data block functions are helper functions for the show employee data function they shouldn't be called outside of that function
function employee_data_block_1($data) {
	    echo "<div class='row bg_1'>";
        echo "<div class='col close_spacing'>";
        echo "<p class='employee_text_1'>".$data."</p>";
        echo "</div>";
        echo "<div class='col'>";
        echo "<p class='employee_text_1'>";
}

function employee_data_block_2() {
        echo "</p>";
        echo "</div>";
        echo "</div>";
}


function show_picture_box($data_id, $fields, $human_labels) {
  global $paths_upload;
  echo "<div class='row justify-content-center'>";
      echo "<div class='col-4 employee_picture_box'>";
        if (!file_exists($paths_upload.$_GET['id'].'.png')) {
          echo "<a href='employee-upload.php?id=".$_GET['id']."'><img width='100%' src='user_image.php'></a>";
        } else {
          echo "<a href='employee-upload.php?id=".$_GET['id']."'><img width='100%' src='user_image.php?id=".$_GET['id']."'></a>";
        }
      echo "</div>";
      echo "<div class='col-8'>";
        show_employee_data($data_id, $fields, $human_labels);
      echo "</div>";
  echo "</div>";
}




//special field function this function is called when a special field i.e. a field not present in
//json file is found it can return many pieces of calculated information such as net salary or employment length

function special_field($data_id ,$field_name) {
	if ($field_name == '*employmentlength') {
		return floor((strtotime('now')-strtotime($_SESSION["employee_data"][$data_id]["employmentstart"])) / 31556736). " years";
	}

}
?>
