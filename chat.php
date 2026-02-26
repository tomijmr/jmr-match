<?php
include('db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$mi_id = $_SESSION['usuario_id'];
$otro_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if ($otro_id == 0) {
    header("Location: pool.php");
    exit();
}

// Obtener datos del otro usuario
$sql = "SELECT nombre, foto1 FROM usuarios WHERE id = $otro_id";
$res = $conn->query($sql);

if ($res->num_rows == 0) {
    echo "Usuario no encontrado";
    exit();
}

$otro_usuario = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo $otro_usuario['nombre']; ?> - JMR Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Fix para móviles: usar dvh si es compatible, fallback a vh */
        body { background-color: #f8f9fa; height: 100vh; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; }
        .chat-header { background: white; padding: 15px; border-bottom: 1px solid #ddd; display: flex; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 10; flex-shrink: 0; }
        .user-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid #ff4b2b; }
        .chat-container { flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; background: #fff5f5; -webkit-overflow-scrolling: touch; }
        .message { max-width: 75%; padding: 10px 15px; border-radius: 20px; position: relative; word-wrap: break-word; font-size: 15px; }
        .msg-me { background: #ff4b2b; color: white; align-self: flex-end; border-bottom-right-radius: 5px; }
        .msg-other { background: #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 5px; }
        .msg-time { display: block; font-size: 10px; margin-top: 5px; opacity: 0.7; text-align: right; }
        .chat-input-area { background: white; padding: 15px; border-top: 1px solid #ddd; display: flex; align-items: center; gap: 10px; flex-shrink: 0; padding-bottom: env(safe-area-inset-bottom); }
        .btn-send { background: #ff4b2b; color: white; border: none; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; transition: 0.2s; flex-shrink: 0; }
        .btn-send:hover { background: #e04022; }
    </style>
</head>
<body>

<!-- Header -->
<div class="chat-header">
    <a href="matchs.php" class="text-secondary me-3"><i class="bi bi-arrow-left fs-4"></i></a>
    <img src="uploads/<?php echo $otro_usuario['foto1']; ?>" class="user-img">
    <h5 class="mb-0 fw-bold"><?php echo $otro_usuario['nombre']; ?></h5>
</div>

<!-- Chat Messages -->
<div class="chat-container" id="chatBox">
    <!-- Messages will be loaded here via AJAX -->
    <div class="text-center text-muted mt-5 loading-msg">Cargando mensajes...</div>
</div>

<!-- Input Area -->
<div class="chat-input-area">
    <input type="text" id="messageInput" class="form-control rounded-pill" placeholder="Escribe un mensaje..." autocomplete="off">
    <button id="sendBtn" type="button" class="btn-send"><i class="bi bi-send-fill"></i></button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const miId = <?php echo $mi_id; ?>;
    const otroId = <?php echo $otro_id; ?>;
    const chatBox = document.getElementById('chatBox');
    let firstLoad = true;

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function loadMessages() {
        $.ajax({
            url: 'chat_api.php',
            type: 'POST',
            data: { action: 'get', otro_id: otroId },
            dataType: 'json',
            success: function(messages) {
                let html = '';
                
                if (messages.length === 0) {
                    html = '<div class="text-center text-muted mt-5">¡Saluda a <?php echo $otro_usuario['nombre']; ?>! 👋</div>';
                } else {
                    messages.forEach(msg => {
                        let typeClass = msg.yo ? 'msg-me' : 'msg-other';
                        html += `
                            <div class="message ${typeClass}">
                                ${msg.msg}
                                <span class="msg-time">${msg.hora}</span>
                            </div>
                        `;
                    });
                }
                
                // Comparar strings sin espacios para evitar falsos positivos
                let currentHTML = $(chatBox).html();
                // Simple check: if length changes significantly or first load
                if (html != '' && (firstLoad || Math.abs(currentHTML.length - html.length) > 10 || currentHTML.indexOf('loading-msg') !== -1)) {
                    
                    // Tolerancia de 50px para scroll
                    let isAtBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 50;
                    
                    $('#chatBox').html(html);

                    if (firstLoad || isAtBottom) {
                        scrollToBottom();
                    }
                    firstLoad = false;
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando mensajes:", error);
                if (firstLoad) {
                    $('#chatBox').html('<div class="text-center text-danger mt-5">Error de conexión. Intenta recargar la página.<br><small>' + error + '</small></div>');
                }
            }
        });
    }

    function sendMessage() {
        let input = $('#messageInput');
        let txt = input.val().trim();
        if (txt === '') return;

        input.val(''); 
        
        // Optimistic UI
        let now = new Date();
        let timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        // Escapar HTML básico de forma segura para display inmediato
        let txtSafe = txt.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        
        $('#chatBox').append(`
            <div class="message msg-me">
                ${txtSafe}
                <span class="msg-time">${timeStr}</span>
            </div>
        `);
        scrollToBottom();

        $.ajax({
            url: 'chat_api.php',
            type: 'POST',
            data: { action: 'send', para_id: otroId, mensaje: txt },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'ok') {
                    loadMessages(); 
                } else {
                    console.error("Error envío:", response);
                    alert("Error al enviar mensaje");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error red envío:", error);
                alert("Error de conexión");
            }
        });
    }

    $(document).ready(function() {
        // Event Listeners seguros
        $('#sendBtn').on('click', function(e) {
            e.preventDefault();
            sendMessage();
        });

        $('#messageInput').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Validar cada 3 segundos nuevos mensajes
        setInterval(loadMessages, 3000);
        
        // Cargar al inicio
        loadMessages();
    });
</script>

</body>
</html>
