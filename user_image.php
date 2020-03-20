<?php
require('paths.php');
if (isset($_GET['id'])) $file = $paths_upload.$_GET['id'].".png";
else $file = $paths_upload."placeholder.png";
header("Content-Type:image/png");
header("Content-length".filesize($file));
readfile($file);
?>
