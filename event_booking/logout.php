<?php
session_start();
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 42000, '/');

echo "Logout successful. Redirecting...";
header("refresh:2;url=login.php");
exit();
?>
