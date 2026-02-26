<?php
// Asegurar buffer de salida y manejo de errores
ob_start();

// Activar JSON
header('Content-Type: application/json; charset=utf-8');

try {
    include('db.php');

    $response = [];

    if (!isset($_SESSION['usuario_id'])) {
        ob_clean(); // Limpiar cualquier echo anterior
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $mi_id = intval($_SESSION['usuario_id']);
    $action = $_POST['action'] ?? '';

    if ($action === 'send') {
        $para_id = isset($_POST['para_id']) ? intval($_POST['para_id']) : 0;
        $msg = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
        
        if ($msg !== '' && $para_id > 0) {
            $msg_safe = $conn->real_escape_string($msg);
            $sql = "INSERT INTO mensajes (usuario_emisor_id, usuario_receptor_id, mensaje) VALUES ($mi_id, $para_id, '$msg_safe')";
            
            if ($conn->query($sql)) {
                $response = ['status' => 'ok'];
            } else {
                throw new Exception("Error BD: " . $conn->error);
            }
        } else {
            throw new Exception("Datos incompletos");
        }
    } elseif ($action === 'get') {
        $otro_id = isset($_POST['otro_id']) ? intval($_POST['otro_id']) : 0;
        
        if ($otro_id > 0) {
            // Marcar como leídos
            $conn->query("UPDATE mensajes SET leido = 1 WHERE usuario_emisor_id = $otro_id AND usuario_receptor_id = $mi_id AND leido = 0");
    
            $sql = "SELECT id, usuario_emisor_id, mensaje, fecha_envio FROM mensajes 
                    WHERE (usuario_emisor_id = $mi_id AND usuario_receptor_id = $otro_id) 
                       OR (usuario_emisor_id = $otro_id AND usuario_receptor_id = $mi_id) 
                    ORDER BY fecha_envio ASC";
            
            $res = $conn->query($sql);
            if (!$res) throw new Exception("Error consulta: " . $conn->error);

            $msgs = [];
            while($row = $res->fetch_assoc()) {
                $msgs[] = [
                    'id' => intval($row['id']),
                    'de' => intval($row['usuario_emisor_id']),
                    'msg' => htmlspecialchars($row['mensaje'], ENT_QUOTES, 'UTF-8'), 
                    'hora' => date('H:i', strtotime($row['fecha_envio'])),
                    'yo' => ($row['usuario_emisor_id'] == $mi_id)
                ];
            }
            // Enviar respuesta array directamente
            ob_clean();
            echo json_encode($msgs);
            exit;
        } else {
             // Enviar array vacío si ID inválido
             ob_clean();
             echo json_encode([]);
             exit;
        }
    } else {
        throw new Exception("Acción desconocida");
    }

    // Respuesta final para acciones que no son 'get' (como 'send')
    ob_clean();
    echo json_encode($response);

} catch (Exception $e) {
    ob_clean(); // Limpiar para enviar solo JSON de error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>