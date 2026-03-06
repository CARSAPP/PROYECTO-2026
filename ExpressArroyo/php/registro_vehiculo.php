<?php
include 'db.php';

// Obtener los valores del formulario
$placa = $_POST['placa'];
$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$cilindrada = $_POST['cilindrada'];
$color = $_POST['color'];
$servicio = $_POST['servicio'];
$clase_vehiculo = $_POST['clase_vehiculo'];
$tipo_carroceria = $_POST['tipo_carroceria'];
$combustible = $_POST['combustible'];
$capacidad = $_POST['capacidad'];
$numero_motor = $_POST['numero_motor'];
$numero_chasis = $_POST['numero_chasis'];
$propietario = $_POST['propietario'];
$identificacion = $_POST['identificacion'];

// Preparar consulta SQL con prepared statement
$sql = "INSERT INTO vehiculos (
    placa, marca, modelo, cilindrada, color, servicio, clase_vehiculo, tipo_carroceria,
    combustible, capacidad, numero_motor, numero_chasis, propietario, identificacion
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssssssss",
    $placa, $marca, $modelo, $cilindrada, $color, $servicio,
    $clase_vehiculo, $tipo_carroceria, $combustible, $capacidad,
    $numero_motor, $numero_chasis, $propietario, $identificacion
);

if ($stmt->execute()) {
    header("Location: ../registroExito.html");
    exit();
} else {
    echo "Error al registrar vehículo: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
