<?php

session_start();
require_once 'config/db.php';


$stmt = $pdo->query("
    SELECT e.id, e.nombre, e.apellido, c.nombre AS carrera,
           e.turno, e.telefono, e.fecha_inscripcion
    FROM estudiantes e
    INNER JOIN carreras c ON e.carrera_id = c.id
    ORDER BY e.apellido ASC, e.nombre ASC
");
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$logueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>UGB - Inscripción</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div>
        <h1>Universidad Gerardo Barrios</h1>
        <span>Sistema de Inscripción de Nuevos Estudiantes</span>
    </div>
</header>

<nav>
    <a href="index.php">Inicio</a>
    <?php if ($logueado): ?>
        <a href="registro.php">Nuevo Estudiante</a>
        <a href="logout.php" class="nav-right">Cerrar Sesión (<?= htmlspecialchars($_SESSION['usuario']) ?>)</a>
    <?php else: ?>
        <a href="login.php" class="nav-right">Iniciar Sesión</a>
    <?php endif; ?>
</nav>

<div class="container">
    <h2>Estudiantes Inscritos</h2>

    <?php if (empty($estudiantes)): ?>
        <p>No hay estudiantes inscritos aún.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Carrera</th>
                <th>Turno</th>
                <th>Teléfono</th>
                <th>Fecha Inscripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estudiantes as $i => $e): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= htmlspecialchars($e['apellido']) ?></td>
                <td><?= htmlspecialchars($e['carrera']) ?></td>
                <td><?= htmlspecialchars($e['turno']) ?></td>
                <td><?= $e['telefono'] ? htmlspecialchars($e['telefono']) : '<em style="color:#aaa;">N/A</em>' ?></td>
                <td><?= htmlspecialchars($e['fecha_inscripcion']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> Universidad Gerardo Barrios &mdash; Sistema de Inscripción
</footer>

</body>
</html>
