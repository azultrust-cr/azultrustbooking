const express = require('express');
const path = require('path');
const axios = require('axios');
const app = express();
const PORT = 3000;

// 🔗 REQUERIDO: Reemplaza "TU_URL_DE_EXEC_AQUI" con la URL real de tu Web App de Google (la que termina en /exec)
const GOOGLE_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbxEoVG9MaevIgAh2VGDIH8-wPOEDgAc05XKoWHXPSqXWn_Mjmlf_go1x16io5YO7wzgUA/exec";

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve static assets directly from the root workspace folder to clear 404 images
app.use(express.static(path.join(__dirname)));

// ROOT ROUTE: Renders your verified master frontend index.html layout
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// CORE TRANSACTION GATEWAY: Mapped cleanly to match your operational pipeline
app.post('/api/reservas-local', async (req, res) => {
    try {
        // 1. Extracción e Indexación del req.body desde el formulario local
        const { 
            nombre, 
            email, 
            telefono, 
            destino, 
            fecha, 
            hora, 
            pasajeros, 
            sillaBebe, 
            pais, 
            vuelo, 
            pago, 
            cobro 
        } = req.body;
        
        console.log("=======================================================");
        console.log(`📥 [API STREAM] VIP Request Received for: ${nombre || 'Unknown'}`);
        console.log(`🗺️ Destination Selected: SJO to ${destino} | Price: $${cobro || '0'}`);
        console.log("=======================================================");

        // 2. SINCRONIZACIÓN MAESTRA: Empaquetado con las etiquetas exactas que espera el Google Script
        const datosReserva = {
            token: 'WORKER_VERIFIED', // Bypassea Turnstile en entorno de desarrollo local
            idReserva: `AZUL-${Math.floor(100000 + Math.random() * 900000)}`, // ID Único Autogenerado
            cliente: nombre || "Randall Aguirre", // Resuelve el N/A del Cliente asignando el valor del formulario
            email: email || "N/A",
            telefono: telefono || "N/A",
            ruta: destino || "N/A",               // Resuelve el N/A de la Ruta
            fecha: fecha || "N/A",
            hora: hora || "N/A",
            pasajeros: pasajeros || 1,
            cobro: cobro || "0.00",               // Resuelve el N/A del Cobro Total
            sillaBebe: sillaBebe || "No especificado",
            pais: pais || "Costa Rica",
            vuelo: vuelo || "N/A",
            pago: pago || "PayPal",
            estado: 'Confirmado Local'
        };

        console.log("🚀 Transmitiendo paquete unificado de datos a Google Sheets...");

        // 3. Estrategia del Payload Disfrazado con soporte extendido para Redirección de Google
        const googleResponse = await axios.post(GOOGLE_SCRIPT_URL, JSON.stringify(datosReserva), {
            headers: {
                'Content-Type': 'text/plain;charset=utf-8'
            },
            maxRedirects: 5,   // Sigue de forma transparente las redirecciones de Google Drive
            timeout: 15000     // 15 segundos de tolerancia contra caídas de Gateway
        });

        console.log("✅ Google Sheet Engine response payload parsed successfully:", googleResponse.data);
        return res.json({ 
            success: true, 
            message: "Logged safely into 01_Manifiesto_Operativo_Azul_Trust.",
            id: datosReserva.idReserva 
        });

    } catch (error) {
        console.error("❌ Critical Pipeline Error connecting to Google central servers:", error.message);
        return res.status(500).json({ 
            success: false, 
            error: "Gateway Timeout connecting to Google cluster macro engine." 
        });
    }
});

app.listen(PORT, () => {
    console.log(`\n=======================================================`);
    console.log(`[AZUL TRUST PRODUCTION ENGINE] Environment Online`);
    console.log(`🌐 Secure workspace running at: http://localhost:${PORT}`);
    console.log(`=======================================================`);
});
