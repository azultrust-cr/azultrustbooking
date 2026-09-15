<?php
// Credenciales locales de Azul Trust - Costo $0 Cloud
$host = 'localhost';
$db   = 'azultrust_db';
$user = 'azultrust_user';
$pass = 'TuContrasenaSegura'; // La contraseña que asignaste en MariaDB
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status_db = "✅ Conexión exitosa a MariaDB local.";
} catch (PDOException $e) {
    $status_db = "❌ Error en base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Azul Trust - Motor de Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; color: #333; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #0056b3; }
        .status { font-weight: bold; padding: 10px; background: #e2e8f0; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>

    <div class="card">
        <h1>Azul Trust Costa Rica</h1>
        <p>Sistema Web y Motor de Reservas VIP en desarrollo local.</p>
        <p>Estatus de Infraestructura:</p>
        <div class="status"><?php echo $status_db; ?></div>
    </div>

</body>
</html>
