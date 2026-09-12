const express = require('express');
const path = require('path');
const axios = require('axios');
const app = express();
const PORT = 3000;

// Google Apps Script Deployment link targeting: 01_Manifiesto_Operativo_Azul_Trust
const GOOGLE_SCRIPT_URL = "https://google.com";

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve the master interactive interface
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// STEP 2 & 4 FLOW: Receiving the reservation payload and logging to the Manifiesto Database
app.post('/api/reservas-local', async (req, res) => {
    try {
        const { nombre, email, telefono, destino, fecha, hora, pasajeros, sillaBebe, pais, vuelo, token, cobro } = req.body;
        
        console.log("=======================================================");
        console.log(`📥 [MANUAL FLOW STEP 2] New Website Request Received!`);
        console.log(`👤 Leader: ${nombre} | 📱 WhatsApp: ${telefono}`);
        console.log(`🗺️ Route: ${destino} | 💰 Final Fee Calculated: $${cobro}`);
        console.log("=======================================================");

        // Unified payload structural framework following your Google Sheets template exactly
        const payload = {
            token: token || "WORKER_VERIFIED", // Gracefully accepts security passkeys for testing loops
            cliente: nombre,
            email: email,
            telefono: telefono,
            ruta: destino,
            fecha: fecha,
            hora: hora,
            pasajeros: pasajeros,
            sillaBebe: sillaBebe,
            pais: pais,
            vuelo: vuelo,
            pago: "PayPal CheckOut",
            cobro: cobro,
            estado: "Confirmado Local"
        };

        console.log("🚀 [MANUAL FLOW STEP 4] Syncing atomic data row to 01_Manifiesto_Operativo_Azul_Trust...");
        const googleResponse = await axios.post(GOOGLE_SCRIPT_URL, payload);

        console.log("✅ Google Database Sincronization Complete:", googleResponse.data);
        res.json({ success: true, message: "Reservation verified and synchronized into the master Manifest spreadsheet!", database: googleResponse.data });

    } catch (error) {
        console.error("❌ Core Pipeline Error during Sheet append execution:", error.message);
        res.status(500).json({ success: false, error: "System unable to establish communication stream with Google Apps Script." });
    }
});

app.listen(PORT, () => {
    console.log(`\n=======================================================`);
    console.log(`🚀 [AZUL TRUST CHANNELS] User Manual Operations Online`);
    console.log(`🌐 Test Gateway active at: http://localhost:${PORT}`);
    console.log(`=======================================================`);
});
