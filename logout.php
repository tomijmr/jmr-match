<?php
include('db.php'); // Esto inicia la sesión si no está iniciada

if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];

    // 1. Obtener imagen para borrar el archivo físico
    $sql = "SELECT foto1 FROM usuarios WHERE id = $id";
    $res = $conn->query($sql);
    
    if ($res && $row = $res->fetch_assoc()) {
        $foto = $row['foto1'];
        if (!empty($foto)) {
            $path = "uploads/" . $foto;
            if (file_exists($path)) {
                unlink($path); // Borrar archivo
            }
        }
    }

    // 2. Eliminar referencias en otras tablas manualmente (para evitar errores de FK si no hay CASCADE)
    $conn->query("DELETE FROM likes WHERE usuario_da_id = $id OR usuario_recibe_id = $id");
    $conn->query("DELETE FROM interacciones WHERE usuario_emisor_id = $id OR usuario_receptor_id = $id");
    
    // Si existe tabla mensajes, borrar también
    $conn->query("DELETE FROM mensajes WHERE usuario_emisor_id = $id OR usuario_receptor_id = $id");

    // 3. Borrar usuario de la base de datos
    $conn->query("DELETE FROM usuarios WHERE id = $id");
}

session_unset();
session_destroy();
header("Location: index.php");
exit();
?>