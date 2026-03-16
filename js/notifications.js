// Sistema de notificaciones JMR Match
let lastCheck = new Date().toISOString().slice(0, 19).replace('T', ' ');
let notificationPermissionRequested = false;

// Intentar pedir permisos en la primera interacción del usuario (click o touch)
document.addEventListener('click', requestNotificationPermission, { once: true });
document.addEventListener('touchstart', requestNotificationPermission, { once: true });

function requestNotificationPermission() {
    if (notificationPermissionRequested) return;
    notificationPermissionRequested = true;

    if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
        Notification.requestPermission().then(permission => {
            console.log("Permiso de notificaciones:", permission);
        });
    }
}

function startNotifications() {
    // Iniciar polling independientemente del permiso, para mostrar Toasts
    pollNotifications();
}

function showNotification(title, body, icon) {
    // 1. Intentar notificación del sistema
    if ("Notification" in window && Notification.permission === "granted") {
        try {
            new Notification(title, {
                body: body,
                icon: icon,
                vibrate: [200, 100, 200]
            });
        } catch (e) {
            console.error("Error mostrando notificación nativa:", e);
        }
    }

    // 2. Mostrar Toastify siempre (aviso visual dentro de la app)
    if (typeof Toastify === 'function') {
        Toastify({
            text: `${title}\n${body}`,
            duration: 4000,
            close: true,
            gravity: "top", // `top` or `bottom`
            position: "right", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
            style: {
                background: "linear-gradient(to right, #FF751F, #e06010)",
                borderRadius: "10px",
                boxShadow: "0 4px 6px rgba(0,0,0,0.3)"
            },
            onClick: function(){} // Callback after click
        }).showToast();
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
                       showNotification("¡Nuevo Match! ❤️‍🔥", `¡Hiciste match con ${match.nombre}!`, 'img/logo4.png');
                    });
                }

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        showNotification(`Mensaje de ${msg.nombre}`, msg.mensaje, 'img/logo4.png');
                    });
                }
            })
            .catch(err => console.error("Error polling notifications:", err));
    }, 5000); // Check cada 5 segundos
}

// Iniciar al cargar
document.addEventListener('DOMContentLoaded', startNotifications);
