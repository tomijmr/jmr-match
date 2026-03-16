// Sistema de notificaciones JMR Match
let lastCheck = new Date().toISOString().slice(0, 19).replace('T', ' ');

function startNotifications() {
    if (!("Notification" in window)) {
        console.log("Este navegador no soporta notificaciones");
        return;
    }

    if (Notification.permission === "granted") {
        pollNotifications();
    } else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                pollNotifications();
            }
        });
    }
}

function pollNotifications() {
    setInterval(() => {
        // Enviar lastCheck codificado
        fetch(`check_notifications.php?last_check=${encodeURIComponent(lastCheck)}`)
            .then(response => response.json())
            .then(data => {
                // Actualizar hora del ultimo check con la del servidor
                if (data.server_time) {
                    lastCheck = data.server_time;
                }

                if (data.matches && data.matches.length > 0) {
                    data.matches.forEach(match => {
                        new Notification("¡Nuevo Match! ❤️‍🔥", {
                            body: `¡Hiciste match con ${match.nombre}!`,
                            icon: 'img/logo4.png'
                        });
                    });
                }

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        new Notification(`Mensaje de ${msg.nombre}`, {
                            body: msg.mensaje,
                            icon: 'img/logo4.png'
                        });
                    });
                }
            })
            .catch(err => console.error("Error polling notifications:", err));
    }, 5000); // Check cada 5 segundos
}

// Iniciar al cargar
document.addEventListener('DOMContentLoaded', startNotifications);
