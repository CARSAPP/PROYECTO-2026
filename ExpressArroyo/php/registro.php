<?php
// Conexión
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "registro";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos en variables
$nombre      = $_POST['nombre'];
$apellido    = $_POST['apellido'];
$cc          = (int) $_POST['cc']; // Se guarda en una variable
$correo      = $_POST['correo'];
$contraseña  = $_POST['contraseña'];
$placa       = $_POST['placa'];

// Preparar la sentencia
$stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido, cc, correo, contraseña, placa)
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $nombre, $apellido, $cc, $correo, $contraseña, $placa);

// Ejecutar
if ($stmt->execute()) {
    header("Location: ../registroExito.html");
    exit();
} else {
    header("Location: ../registro.html?error=Error al registrar: " . $conn->error);
    exit();
}

// Cerrar
$stmt->close();
$conn->close();
?>
