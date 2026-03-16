self.addEventListener('push', function(event) {
    // Si usáramos Push API real (servidor -> navegador), aquí manejaríamos el evento.
    // Pero como estamos usando polling desde el cliente, este evento no se dispara por ahora.
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Si ya hay una pestaña abierta, enfocarla
            if (clientList.length > 0) {
                let client = clientList[0];
                for (let i = 0; i < clientList.length; i++) {
                    if (clientList[i].focused) {
                        return clientList[i].focus();
                    }
                }
                return client.focus();
            }
            // Si no, abrir una nueva (opcional, ajusta la URL según necesites)
            if (clients.openWindow) {
                return clients.openWindow('/matchs.php'); 
            }
        })
    );
});
