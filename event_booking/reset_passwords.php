<?php
include 'config.php';

$new_password_admin = password_hash("passwordadmin", PASSWORD_DEFAULT);
$conn->query("UPDATE users SET password='$new_password_admin' WHERE email='admin@example.com'");

$new_password_user = password_hash("passwordjohn", PASSWORD_DEFAULT);
$conn->query("UPDATE users SET password='$new_password_user' WHERE email='john@example.com'");

echo "✅ Passwords updated successfully! Delete this file now.";
?>
