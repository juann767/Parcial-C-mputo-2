<?php
// registro.php - Formulario protegido para inscribir nuevos estudiantes
session_start();

// Solo usuarios logueados pueden acceder
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

$error   = '';
$success = '';
$errores = [];

// Cargar carreras para el select
$carreras = $pdo->query("SELECT id, nombre FROM carreras ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Recoger y sanear datos ---
    $nombre    = trim($_POST['nombre']    ?? '');
    $apellido  = trim($_POST['apellido']  ?? '');
    $carrera_id = (int)($_POST['carrera_id'] ?? 0);
    $turno     = trim($_POST['turno']     ?? '');
    $telefono  = trim($_POST['telefono']  ?? '');
    $fecha     = trim($_POST['fecha_inscripcion'] ?? '');

    // --- Validaciones ---
    if (empty($nombre) || strlen($nombre) < 2)
        $errores[] = 'El nombre debe tener al menos 2 caracteres.';

    if (empty($apellido) || strlen($apellido) < 2)
        $errores[] = 'El apellido debe tener al menos 2 caracteres.';

    if ($carrera_id <= 0)
        $errores[] = 'Seleccione una carrera válida.';

    $turnos_validos = ['Matutino', 'Vespertino', 'Nocturno'];
    if (!in_array($turno, $turnos_validos))
        $errores[] = 'Seleccione un turno válido.';

    // Teléfono es opcional, pero si se ingresa debe tener formato correcto
    if (!empty($telefono) && !preg_match('/^\d{8}$/', $telefono))
        $errores[] = 'El teléfono debe tener exactamente 8 dígitos numéricos.';

    if (empty($fecha) || !strtotime($fecha))
        $errores[] = 'La fecha de inscripción no es válida.';

    // Verificar que la carrera existe
    if ($carrera_id > 0) {
        $check = $pdo->prepare("SELECT id FROM carreras WHERE id = ?");
        $check->execute([$carrera_id]);
        if (!$check->fetch()) $errores[] = 'La carrera seleccionada no existe.';
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("
            INSERT INTO estudiantes (nombre, apellido, carrera_id, turno, telefono, fecha_inscripcion)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nombre,
            $apellido,
            $carrera_id,
            $turno,
            empty($telefono) ? null : $telefono,
            $fecha
        ]);
        $success = "Estudiante $nombre $apellido inscrito correctamente.";
        // Limpiar los POST
        $_POST = [];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Estudiante - UGB</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div>
        <h1>Universidad Gerardo Barrios</h1>
        <span>Sistema de Inscripción</span>
    </div>
</header>

<nav>
    <a href="index.php">Inicio</a>
    <a href="registro.php">Nuevo Estudiante</a>
    <a href="logout.php" class="nav-right">Cerrar Sesión (<?= htmlspecialchars($_SESSION['usuario']) ?>)</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Inscribir Nuevo Estudiante</h2>

        <?php if ($errores): ?>
            <div class="alert alert-error">
                <ul style="margin-left:15px;">
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="registro.php">

            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                       maxlength="80" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido *</label>
                <input type="text" id="apellido" name="apellido"
                       value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                       maxlength="80" required>
            </div>

            <!-- INPUT TIPO SELECT -->
            <div class="form-group">
                <label for="carrera_id">Carrera *</label>
                <select id="carrera_id" name="carrera_id" required>
                    <option value="">-- Seleccione una carrera --</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (($_POST['carrera_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- INPUT TIPO RADIO -->
            <div class="form-group">
                <label>Turno *</label>
                <div class="radio-group">
                    <?php foreach (['Matutino','Vespertino','Nocturno'] as $t): ?>
                    <label>
                        <input type="radio" name="turno" value="<?= $t ?>"
                            <?= (($_POST['turno'] ?? '') === $t) ? 'checked' : '' ?> required>
                        <?= $t ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono <em style="font-weight:normal;">(opcional, 8 dígitos)</em></label>
                <input type="text" id="telefono" name="telefono"
                       value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                       maxlength="8" placeholder="Ej: 78901234">
            </div>

            <div class="form-group">
                <label for="fecha_inscripcion">Fecha de Inscripción *</label>
                <input type="date" id="fecha_inscripcion" name="fecha_inscripcion"
                       value="<?= htmlspecialchars($_POST['fecha_inscripcion'] ?? date('Y-m-d')) ?>"
                       required>
            </div>

            <button type="submit">Guardar Inscripción</button>
        </form>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Universidad Gerardo Barrios
</footer>

</body>
</html>
