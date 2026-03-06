<?php
session_start();

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sólo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.html");
    exit();
}

// 1️⃣ Recibir datos
$rol        = $_POST['rol']        ?? '';
$correo     = $_POST['correo']     ?? '';
$contraseña = $_POST['contraseña'] ?? '';

if (empty($rol) || empty($correo) || empty($contraseña)) {
    echo <<<HTML
<script>
  alert('❗ Debes completar todos los campos.');
  history.back();
</script>
HTML;
    exit();
}

// 2️⃣ Conexión
$conn = new mysqli("localhost", "root", "", "registro");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 3️⃣ Elegir tabla y página de redirección
if ($rol === 'conductor') {
    $sql = "SELECT id, nombre, contraseña FROM usuario WHERE correo = ?";
    $redirectPage = 'Bienvenido.html';
} elseif ($rol === 'administrador') {
    $sql = "SELECT id, nombre, contraseña FROM usuario_admi WHERE correo = ?";
    $redirectPage = 'BienvenidoAdmin.html';
} else {
    echo <<<HTML
<script>
  alert('⚠️ Rol no válido.');
  history.back();
</script>
HTML;
    exit();
}

// 4️⃣ Preparar y ejecutar
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($contraseña === $row['contraseña']) {
        // 5️⃣ Sesión PHP
        $_SESSION['id_usuario'] = $row['id'];
        $_SESSION['nombre']     = $row['nombre'];

        // 6️⃣ JavaScript: marcar loggedIn y guardar nombre puro
        //    Usamos json_encode para escapar correctamente
        $jsName = json_encode($row['nombre'], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);

        echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Redirigiendo…</title></head>
<body>
<script>
  // Marca al usuario como logueado
  sessionStorage.setItem('loggedIn', 'true');
  // Guarda el nombre directamente
  sessionStorage.setItem('nombre', {$jsName});
  // Redirige sin parámetros
  window.location.replace("../{$redirectPage}");
</script>
<p>Redirigiendo…</p>
</body>
</html>
HTML;
        exit();
    } else {
        echo <<<HTML
<script>
  alert('🔒 Contraseña incorrecta.');
  history.back();
</script>
HTML;
        exit();
    }
} else {
    echo <<<HTML
<script>
  alert('🚫 Correo no registrado.');
  history.back();
</script>
HTML;
    exit();
}

// Cerrar
$stmt->close();
$conn->close();
?>
