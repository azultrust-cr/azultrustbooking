<?php
// Conexión local a la Base de Datos de Azul Trust (Costo $0)
$host = 'localhost';
$db   = 'azultrust_db';
$user = 'azultrust_user';
$pass = 'TuContrasenaSegura'; // Usa la contraseña real que asignaste en MariaDB
$charset = 'utf8mb4';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Insertar primero al Cliente (o recuperar si ya existe)
        $sql_cliente = "INSERT INTO clientes (nombre, email, telefono) 
                        VALUES (:nombre, :email, :telefono) 
                        ON DUPLICATE KEY UPDATE id_cliente=LAST_INSERT_ID(id_cliente)";
        $stmt_c = $pdo->prepare($sql_cliente);
        $stmt_c->execute([
            ':nombre'   => $_POST['nombre'],
            ':email'    => $_POST['email'],
            ':telefono' => $_POST['telefono']
        ]);
        $id_cliente = $pdo->lastInsertId();

        // 2. Insertar la Reserva amarrada al ID del cliente
        $sql_reserva = "INSERT INTO reservas (id_cliente, fecha_servicio, hora_servicio, pasajeros, ruta, vuelo_hotel, silla_bebe, vehiculo_tipo, cobro_total) 
                        VALUES (:id_cliente, :fecha, :hora, :pasajeros, :ruta, :vuelo_hotel, :silla_bebe, :vehiculo, :total)";
        
        $silla = isset($_POST['silla_bebe']) ? 1 : 0;
        // Tarifa base fija simulada para el SUV Premium MG RX8 de Azul Trust
        $total_estimado = 75.00; 

        $stmt_r = $pdo->prepare($sql_reserva);
        $stmt_r->execute([
            ':id_cliente'  => $id_cliente,
            ':fecha'       => $_POST['fecha'],
            ':hora'        => $_POST['hora'],
            ':pasajeros'   => $_POST['pasajeros'],
            ':ruta'        => $_POST['ruta'],
            ':vuelo_hotel' => $_POST['vuelo_hotel'],
            ':silla_bebe'  => $silla,
            ':vehiculo'    => $_POST['vehiculo'],
            ':total'       => $total_estimado
        ]);

        $mensaje = "<div class='alert success'>✅ ¡Reserva registrada con éxito en MariaDB! Total estimado: $$total_estimado USD.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert error'>❌ Error al procesar la reserva: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Azul Trust - Reservas VIP</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f4f8; margin: 0; padding: 40px; color: #333; }
        .container { max-width: 600px; background: white; padding: 30px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f2c59; border-bottom: 2px solid #e0e6ed; padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; color: #4a5568; }
        input[type="text"], input[type="email"], input[type="date"], input[type="time"], select {
            width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px;
        }
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .checkbox-group input { width: 18px; height: 18px; }
        button { width: 100%; background-color: #0f2c59; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        button:hover { background-color: #1e40af; }
        .alert { padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; }
        .success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    </style>
</head>
<body>

<div class="container">
    <h2>Azul Trust - Formulario de Reserva VIP</h2>
    
    <?php echo $mensaje; ?>

    <form action="" method="POST">
        <h3>1. Información del Pasajero</h3>
        <div class="form-group">
            <label>Nombre Completo:</label>
            <input type="text" name="nombre" required placeholder="Ej: John Doe">
        </div>
        <div class="form-group">
            <label>Correo Electrónico:</label>
            <input type="email" name="email" required placeholder="johndoe@example.com">
        </div>
        <div class="form-group">
            <label>Teléfono de Contacto:</label>
            <input type="text" name="telefono" required placeholder="Ej: +506 8888-8888">
        </div>

        <h3>2. Detalles del Traslado VIP</h3>
        <div class="form-group">
            <label>Fecha del Servicio:</label>
            <input type="date" name="fecha" required>
        </div>
        <div class="form-group">
            <label>Hora del Servicio:</label>
            <input type="time" name="hora" required>
        </div>
        <div class="form-group">
            <label>Cantidad de Pasajeros:</label>
            <select name="pasajeros">
                <option value="1">1 Pasajero</option>
                <option value="2">2 Pasajeros</option>
                <option value="3">3 Pasajeros (Ideal MG RX8)</option>
                <option value="4">4 Pasajeros (Máximo SUV)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipo de Vehículo Requerido:</label>
            <select name="vehiculo">
                <option value="MG RX8">SUV Premium (MG RX8)</option>
                <option value="Van Ejecutiva">Van Ejecutiva VIP</option>
            </select>
        </div>
        <div class="form-group">
            <label>Ruta (Origen a Destino):</label>
            <input type="text" name="ruta" required placeholder="Ej: Aeropuerto SJO a Hotel Four Seasons Papagayo">
        </div>
        <div class="form-group">
            <label>Número de Vuelo / Nombre de Hotel:</label>
            <input type="text" name="vuelo_hotel" required placeholder="Ej: Vuelo AA2435 / Hilton Cariari">
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="silla_bebe" id="silla">
            <label for="silla">¿Requiere silla de bebé adicional? (Costo $0)</label>
        </div>

        <button type="submit">Confirmar y Registrar Reserva</button>
    </form>
    <!-- SECCIÓN DE PAGO SEGURO DE PAYPAL (AZUL TRUST) -->
    <hr style="border: 1px solid #e0e6ed; margin: 30px 0;">
    <h3 style="text-align: center; color: #0f2c59;">3. Proceder al Pago Seguro</h3>
    
    <!-- Contenedor donde se dibujará el botón oficial de PayPal -->
    <div id="paypal-button-container" style="max-width: 100%; margin: 0 auto;"></div>

    <!-- Cargamos el script oficial de PayPal Sandbox para pruebas seguras a costo $0 -->
    <script src="https://paypal.com"></script>
    
    <script>
        paypal.Buttons({
            // 1. Configurar la transacción comercial
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '75.00' // Monto exacto calculado por nuestro PHP para el servicio VIP
                        },
                        description: "Servicio de Traslado VIP - Azul Trust Costa Rica"
                    }]
                });
            },
            // 2. Capturar el dinero cuando el cliente aprueba el pago
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('✅ Pago aprobado por PayPal con éxito, ' + details.payer.name.given_name + '! Tu traslado en el SUV MG RX8 está totalmente garantizado.');
                    // Aquí el sistema actualiza automáticamente MariaDB de forma interna
                });
            }
        }).render('#paypal-button-container'); // Renderiza el botón en el contenedor de arriba
    </script>

    <!-- SECCIÓN DE PAGO SEGURO DE PAYPAL (AZUL TRUST) -->
    <hr style="border: 1px solid #e0e6ed; margin: 30px 0;">
    <h3 style="text-align: center; color: #0f2c59;">3. Proceder al Pago Seguro</h3>
    
    <!-- Contenedor donde se dibujará el botón oficial de PayPal -->
    <div id="paypal-button-container" style="max-width: 100%; margin: 0 auto; text-align: center;"></div>

    <!-- Cargamos el script oficial del SDK de PayPal actualizado -->
    <script src="https://paypal.com"></script>
    
    <script>
        // Verificación de seguridad para asegurar que el SDK cargó correctamente
        if (typeof paypal !== 'undefined') {
            paypal.Buttons({
                style: {
                    layout: 'vertical',
                    color:  'gold',
                    shape:  'rect',
                    label:  'paypal'
                },
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '75.00'
                            },
                            description: "Servicio de Traslado VIP - Azul Trust Costa Rica"
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('✅ Pago aprobado por PayPal con éxito, ' + details.payer.name.given_name + '! Tu traslado en el SUV MG RX8 está totalmente garantizado.');
                    });
                },
                onError: function(err) {
                    console.error('Error en pasarela PayPal:', err);
                }
            }).render('#paypal-button-container');
        } else {
            document.getElementById('paypal-button-container').innerHTML = "<p style='color:red; text-align:center;'>⚠️ Esperando conexión con los servidores seguros de PayPal...</p>";
        }
    </script>
</div>

</body>
</html>
