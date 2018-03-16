<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

echo exec("ls")
if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
	phpinfo();
}
//echo phpinfo();
?>
