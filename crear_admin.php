<?php


require_once 'config/db.php';

$username = 'admin';
$password = 'admin123';
$hash     = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hash]);

if ($stmt->rowCount() > 0) {
    echo '<p style="font-family:Arial;color:green;padding:20px;">
        Usuario <strong>admin</strong> creado correctamente.<br>
        Contraseña: <strong>admin123</strong><br><br>
        <strong>Elimine este archivo ahora.</strong>
    </p>';
} else {
    echo '<p style="font-family:Arial;color:orange;padding:20px;">
        El usuario <strong>admin</strong> ya existe.
    </p>';
}
?>
