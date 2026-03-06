<?php
session_start();

// Solo administradores logueados pueden agregar otro admin
if (empty($_SESSION['id_usuario'])) {
    die("Error: No estás autenticado.");
}

// 1️⃣ Recibir y limpiar datos (¡ojo al nombre de índice POST!)
$nombre     = trim($_POST['nombre']     ?? '');
$apellido   = trim($_POST['apellido']   ?? '');
$cedula     = trim($_POST['cedula']     ?? '');
$correo     = trim($_POST['correo']     ?? '');
// Cambiamos aquí para coincidir con name="contrasena" en el HTML
$contrasena = trim($_POST['contrasena'] ?? '');

// 2️⃣ Validar
if ($nombre === '' || $apellido === '' || $cedula === '' || $correo === '' || $contrasena === '') {
    echo "<script>
            alert('❗ Todos los campos son obligatorios.');
            history.back();
          </script>";
    exit;
}

// 3️⃣ Conectar a la base
$conn = new mysqli("localhost","root","","registro");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 4️⃣ Preparar inserción
// Asegúrate de que en la tabla tu columna se llama exactamente 'contraseña' (con tilde)
// o cámbiala en la consulta si la renombraste a 'contrasena'.
$stmt = $conn->prepare("
    INSERT INTO usuario_admi
      (nombre, apellido, cedula, correo, contraseña)
    VALUES (?, ?, ?, ?, ?)
");
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
// Bind usando la variable $contrasena (sin ñ ni tilde en PHP)
$stmt->bind_param(
    "sssss",
    $nombre,
    $apellido,
    $cedula,
    $correo,
    $contrasena
);

// 5️⃣ Ejecutar y responder
if ($stmt->execute()) {
    echo "<script>
            alert('✅ Administrador registrado correctamente.');
            window.location.href = '../conductores.html';
          </script>";
} else {
    $error = addslashes($stmt->error);
    echo "<script>
            alert('❌ Error al registrar: {$error}');
            history.back();
          </script>";
}

$stmt->close();
$conn->close();
