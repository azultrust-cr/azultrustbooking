const express = require('express');
const app = express();
const PORT = 3000;

// Mensaje de bienvenida para el motor de reservas
app.get('/', (req, res) => {
    res.send('<h1>Servidor de Reservas Azul Trust - ¡En línea!</h1>');
});

// Encender el servidor en tu computadora Ubuntu
app.listen(PORT, () => {
    console.log(`Servidor corriendo en: http://localhost:${PORT}`);
});
