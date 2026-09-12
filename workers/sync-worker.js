const axios = require('axios');

// Enlace de conexión directo a tu macro de Google Sheets
const GOOGLE_SCRIPT_URL = "https://google.com";

// Frecuencia de ciclo: Comprobar el estado cada 15 segundos
const SYNC_INTERVAL = 15000; 

console.log("=======================================================");
console.log("🚀 [AZUL TRUST SYSTEM] Background Sync Suite Active");
console.log(`📡 Tracker status: ONLINE | Checking cycle pace: Every ${SYNC_INTERVAL / 1000}s`);
console.log("=======================================================");

// Función de automatización en segundo plano
async function checkDatabaseSynchronicity() {
    try {
        const timestamp = new Date().toLocaleTimeString();
        console.log(`\n[${timestamp}] 🔄 Syncing stream states with Google Sheet Engine...`);

        // Realizamos la consulta GET a tu Google Apps Script
        const response = await axios.get(GOOGLE_SCRIPT_URL);
        
        // Verificación flexible: Si Google responde con datos válidos o estatus 200 (Éxito)
        if (response.status === 200) {
            console.log("✅ Synchronization Channel Stable. Connection to Google Sheets is healthy!");
            // Imprime en la terminal lo que responde Google para que lo veas en vivo
            console.log(`💬 Sheet Data Response: ${JSON.stringify(response.data)}`);
        } else {
            console.log("⚠️ Unexpected channel code response. Reviewing web app configuration...");
        }

    } catch (error) {
        console.error("❌ Sync Error: Communication gateway timed out. Check connection setup.", error.message);
    }
}

// Ejecutar el rastreador inmediatamente al iniciar
checkDatabaseSynchronicity();

// Ciclo infinito automatizado a costo $0
setInterval(checkDatabaseSynchronicity, SYNC_INTERVAL);
