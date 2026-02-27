<?php
include('db.php'); // Esto inicia la sesión si no está iniciada

if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];

    // 1. Obtener imagen para borrar el archivo físico
    $sql = "SELECT foto1 FROM usuarios WHERE id = $id";
    $res = $conn->query($sql);
    
    if ($res && $row = $res->fetch_assoc()) {
        $foto = $row['foto1'];
        if (!empty($foto) && file_exists("uploads/" . $foto)) {
            unlink("uploads/" . $foto); // Borrar archivo
        }
    }

    // 2. Borrar usuario de la base de datos
    // Gracias a ON DELETE CASCADE en las otras tablas (likes, mensajes), todo se borra
    $conn->query("DELETE FROM usuarios WHERE id = $id");
}

session_destroy();
header("Location: index.php");
exit();
?>