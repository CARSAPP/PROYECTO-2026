<?php
session_start();

// 1️⃣ Verificar sesión
if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
    die("Error: Usuario no autenticado.");
}
$usuario_id = $_SESSION['id_usuario'];

// 2️⃣ Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "registro");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 3️⃣ Recibir y validar datos del formulario
$origen_viaje      = trim($_POST['origen_viaje']    ?? '');
$destino_viaje     = trim($_POST['destino_viaje']   ?? '');
$fecha_formulario  = $_POST['fecha_formulario']    ?? '';
$flete             = floatval($_POST['flete']      ?? 0);
$porcentaje        = intval  ($_POST['porcentaje'] ?? 0);

if ($origen_viaje === '' || $destino_viaje === '' || $fecha_formulario === '') {
    die("Error: Debes rellenar origen, destino y fecha del formulario.");
}

// 4️⃣ Calcular totales
$total_gastos       = 0;
if (!empty($_POST['valor_gasto'])) {
    foreach ($_POST['valor_gasto'] as $val) {
        $total_gastos += floatval($val);
    }
}
$sobrante_flete     = $flete - $total_gastos;
$ganancia_conductor = $flete * ($porcentaje / 100);

// 5️⃣ Insertar en tabla `formulario`
$sql = "
  INSERT INTO formulario
    (usuario_id,
     origen_viaje,
     destino_viaje,
     fecha_formulario,
     flete_calculado,
     porcentaje,
     total_gastos,
     sobrante_flete,
     ganancia_conductor)
  VALUES (?,?,?,?,?,?,?,?,?)
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error al preparar formulario: " . $conn->error);
}
$stmt->bind_param(
    "isssdiddd",
    $usuario_id,
    $origen_viaje,
    $destino_viaje,
    $fecha_formulario,
    $flete,
    $porcentaje,
    $total_gastos,
    $sobrante_flete,
    $ganancia_conductor
);
if (!$stmt->execute()) {
    die("Error al insertar formulario: " . $stmt->error);
}
$formulario_id = $stmt->insert_id;
$stmt->close();

// 6️⃣ Insertar cada gasto (igual que antes)
if (!empty($_POST['tipo_gasto']) && !empty($_POST['valor_gasto'])) {
    $sqlG = "INSERT INTO gasto (formulario_id, tipo_gasto, valor_gasto) VALUES (?, ?, ?)";
    $stmtG = $conn->prepare($sqlG);
    if (!$stmtG) {
        die("Error al preparar gastos: " . $conn->error);
    }
    foreach ($_POST['tipo_gasto'] as $i => $tipo) {
        $tipo  = trim($tipo);
        $valor = floatval($_POST['valor_gasto'][$i] ?? 0);
        if ($tipo === '' || $valor <= 0) continue;
        $stmtG->bind_param("isd", $formulario_id, $tipo, $valor);
        $stmtG->execute();
    }
    $stmtG->close();
}

// 7️⃣ Redirigir al éxito
header("Location: ../Bienvenido.html?mensaje=Formulario+guardado");
exit;
?>
