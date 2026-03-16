<?php
session_start();
include('db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No session']);
    exit;
}

$my_id = $_SESSION['usuario_id'];
// Timestamp from client or default to 5 seconds ago
$last_check = isset($_GET['last_check']) ? $_GET['last_check'] : date('Y-m-d H:i:s', time() - 10); 

$response = [
    'matches' => [],
    'messages' => [],
    'server_time' => date('Y-m-d H:i:s')
];

// 1. Check for NEW Matches (where I received a like recently that completes a match)
// A match is when I receive a like (l_in) AND I have already given a like (l_out) to that person.
// We check if the incoming like is recent.
$sql_matches = "SELECT u.nombre 
                FROM likes l_in 
                JOIN likes l_out ON l_in.usuario_da_id = l_out.usuario_recibe_id 
                JOIN usuarios u ON l_in.usuario_da_id = u.id
                WHERE l_in.usuario_recibe_id = $my_id 
                AND l_out.usuario_da_id = $my_id 
                AND l_in.fecha > '$last_check'";

$res_matches = $conn->query($sql_matches);
if ($res_matches) {
    while ($row = $res_matches->fetch_assoc()) {
        $response['matches'][] = $row;
    }
}

// 2. Check for NEW Messages
$sql_messages = "SELECT u.nombre, m.mensaje 
                 FROM mensajes m
                 JOIN usuarios u ON m.usuario_emisor_id = u.id
                 WHERE m.usuario_receptor_id = $my_id 
                 AND m.leido = 0 
                 AND m.fecha_envio > '$last_check'";

$res_messages = $conn->query($sql_messages);
if ($res_messages) {
    while ($row = $res_messages->fetch_assoc()) {
        $response['messages'][] = $row;
    }
}

echo json_encode($response);
?>