<?php
$host = '127.0.0.1';
$user = 'juan123';
$pass = '12345678Perro';
$base = 'ugb_inscripcion';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$base;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('<p style="color:red;text-align:center;">Error de conexión a la base de datos. Verifique la configuración.</p>');
}
?>