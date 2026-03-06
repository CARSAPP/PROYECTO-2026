<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['test'] = "Sesión activa";
echo "Sesión actual: ";
print_r($_SESSION);
?>
