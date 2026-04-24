<?php

session_start();


if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Por favor complete todos los campos.';
    } else {
        // Usar prepared statement para evitar SQL injection
        $stmt = $pdo->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario']    = $usuario['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - UGB Inscripción</title>
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
    <a href="index.php">← Volver al inicio</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Iniciar Sesión</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" maxlength="100" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Universidad Gerardo Barrios
</footer>

</body>
</html>
