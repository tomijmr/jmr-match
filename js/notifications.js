// Sistema de notificaciones JMR Match
let lastCheck = null; // Iniciar en null para que el servidor decida la hora inicial
let notificationPermissionRequested = false;
let audioContext = null;

// Intentar pedir permisos y habilitar audio en la primera interacción
document.addEventListener('click', enableNotificationsAndAudio, { once: true });
document.addEventListener('touchstart', enableNotificationsAndAudio, { once: true });

function enableNotificationsAndAudio() {
    if (notificationPermissionRequested) return;
    notificationPermissionRequested = true;

    // 1. Pedir permiso de Notificaciones
    if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
        Notification.requestPermission().then(permission => {
            console.log("Permiso de notificaciones:", permission);
        });
    }

    // 2. Inicializar AudioContext
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            audioContext = new AudioContext();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            gainNode.gain.value = 0; 
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.start(0);
            oscillator.stop(0.001);
        }
    } catch (e) { console.error(e); }

    // 3. Registrar Service Worker para notificaciones móviles (Si aplica)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js')
        .then(function(reg) {
            console.log('Service Worker registrado', reg);
        }).catch(function(err) {
            console.log('Error al registrar SW', err);
        });
    }
}

function playNotificationSound() {
    if (!audioContext) return;
    
    // Si el contexto está suspendido (común en mobile), intentar reanudarlo
    if (audioContext.state === 'suspended') {
        audioContext.resume();
    }

    try {
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
        oscillator.frequency.exponentialRampToValueAtTime(440, audioContext.currentTime + 0.5); // Baja a A4
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.5);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    } catch (e) {
        console.error("Error reproduciendo sonido:", e);
    }
}

function startNotifications() {
    // Iniciar polling independientemente del permiso
    pollNotifications();
}

function showNotification(title, body, icon) {
    // 0. Reproducir sonido y vibrar
    playNotificationSound();
    if ("vibrate" in navigator) {
        navigator.vibrate([200, 100, 200]);
    }

    // 1. Intentar notificación del sistema (Preferiblemente via Service Worker en Android)
    if ("Notification" in window && Notification.permission === "granted") {
        if ('serviceWorker' in navigator) {
            // Intentar usar el Service Worker si está registrado, incluso si no controla (aún) la página
            navigator.serviceWorker.ready.then(function(registration) {
                registration.showNotification(title, {
                    body: body,
                    icon: icon,
                    vibrate: [200, 100, 200],
                    tag: 'match-notification',
                    renotify: true
                });
            }).catch(function(e) {
                // Fallback si falla el SW
                console.error("Fallo notification SW:", e);
                try {
                    new Notification(title, {
                        body: body,
                        icon: icon,
                        vibrate: [200, 100, 200]
                    });
                } catch (e2) { console.error(e2); }
            });
        } else {
            try {
                new Notification(title, {
                    body: body,
                    icon: icon,
                    vibrate: [200, 100, 200]
                });
            } catch (e) { console.error(e); }
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
        // Enviar lastCheck solo si lo tenemos, sino el servidor usa su hora actual
        let url = 'check_notifications.php';
        if (lastCheck) {
            url += `?last_check=${encodeURIComponent(lastCheck)}`;
        }

        fetch(url)
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
