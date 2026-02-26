<?php
include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    de_usuario_id INT NOT NULL,
    para_usuario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido TINYINT(1) DEFAULT 0,
    FOREIGN KEY (de_usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (para_usuario_id) REFERENCES usuarios(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'mensajes' creada exitosamente";
} else {
    echo "Error creando tabla: " . $conn->error;
}
?>