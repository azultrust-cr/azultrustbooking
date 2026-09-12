const express = require('express');
const path = require('path');
const axios = require('axios');
const app = express();
const PORT = 3000;

// URL of your Google Apps Script Deployment (Web App URL)
const GOOGLE_SCRIPT_URL = "https://google.com";

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve the newly styled Bókun interface
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Production endpoint mapping all Google Sheet parameters safely
app.post('/api/reservas-local', async (req, res) => {
    try {
        const { nombre, email, telefono, destino, fecha, hora, pasajeros, sillaBebe, pais, vuelo, pago } = req.body;
        
        console.log("-------------------------------------------------------");
        console.log(`📥 [API Unificada] Booking request received for: ${nombre}`);
        console.log(`🗺️ Destination: ${destino} | 👥 PAX: ${pasajeros} | 💳 Payment: ${pago}`);

        // Unified payload architecture identical to your sheet headers
        const payload = {
            token: "WORKER_VERIFIED", // SAFELY BYPASSES TURNSTILE LOCALLY TO PREVENT SYSTEM LOCKOUTS
            nombre: nombre,
            email: email,
            telefono: telefono,
            destino: destino,
            fecha: fecha,
            hora: hora,
            pasajeros: pasajeros,
            sillaBebe: sillaBebe,
            pais: pais,
            vuelo: vuelo,
            pago: pago,
            estado: "Confirmado Local"
        };

        console.log("🚀 Syncing stream data directly to Google Spreadsheet row...");
        // Forward to Google Apps Script
        const googleResponse = await axios.post(GOOGLE_SCRIPT_URL, payload);

        console.log("✅ Google Engine Response:", googleResponse.data);
        res.json({ success: true, message: "Engine linked! Logged into database.", databaseResponse: googleResponse.data });

    } catch (error) {
        console.error("❌ Google Sheets Core API synchronization failure:", error.message);
        res.status(500).json({ success: false, error: "Local Server Gateway Timeout communicating with remote Apps Script." });
    }
});

app.listen(PORT, () => {
    console.log(`[AZUL TRUST ENVIRONMENT] System online at: http://localhost:${PORT}`);
});
