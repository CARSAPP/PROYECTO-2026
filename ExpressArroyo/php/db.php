<?php
$host = "localhost";
$user = "root";
$password = "";   // sin contraseña
$db = "registro"; // verifica que sea el nombre correcto

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
