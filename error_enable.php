<?php
  //error reporting set $display_oopsies to true to enable error logging
  //session_start();
  $display_oopsies = $_SESSION["display_oopsies"];
  if ($display_oopsies) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
  } else {
	error_reporting(0);
  }
?>
