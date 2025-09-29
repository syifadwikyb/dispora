<?php
session_start();
session_unset();
session_destroy();
header("Location: /ams/login.php"); 
exit();
?>