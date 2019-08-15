<?php


define( 'DB_HOST', 'localhost' );          // Set database host
define( 'DB_USER', 'root' );             // Set database user
define( 'DB_PASS', '' );             // Set database password
define( 'DB_NAME', 'systemx_db' );  

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);


?>