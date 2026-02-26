<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Chat</h1>";

// 1. Check DB Connection
echo "<h2>1. Conexión a Base de Datos</h2>";
try {
    if (!file_exists('db.php')) {
        throw new Exception("El archivo db.php no existe.");
    }
    include('db.php');
    echo "<p style='color:green'>✅ Archivo db.php incluido.</p>";
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    echo "<p style='color:green'>✅ Conexión a MySQL exitosa.</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    die();
}

// 2. Check Session
echo "<h2>2. Sesión</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['usuario_id'])) {
    echo "<p style='color:green'>✅ Sesión activa. Usuario ID: " . $_SESSION['usuario_id'] . "</p>";
} else {
    echo "<p style='color:orange'>⚠️ No hay sesión de usuario activa. (Esto es normal si no te has logueado, pero el chat fallará).</p>";
}

// 3. Check Table Structure
echo "<h2>3. Estructura de Tabla 'mensajes'</h2>";
$table_check = $conn->query("SHOW TABLES LIKE 'mensajes'");
if ($table_check->num_rows > 0) {
    echo "<p style='color:green'>✅ La tabla 'mensajes' existe.</p>";
    
    $columns = $conn->query("SHOW COLUMNS FROM mensajes");
    echo "<ul>";
    while ($col = $columns->fetch_assoc()) {
        echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>❌ La tabla 'mensajes' NO existe.</p>";
    echo "<p>Ejecuta este SQL en phpMyAdmin:</p>";
    echo "<pre>
    CREATE TABLE IF NOT EXISTS mensajes (
      id int(11) NOT NULL AUTO_INCREMENT,
      de_usuario_id int(11) NOT NULL,
      para_usuario_id int(11) NOT NULL,
      mensaje text NOT NULL,
      fecha_envio timestamp NULL DEFAULT current_timestamp(),
      leido tinyint(1) DEFAULT 0,
      PRIMARY KEY (id),
      KEY de_usuario_id (de_usuario_id),
      KEY para_usuario_id (para_usuario_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    </pre>";
    
    // Attempt to create it automatically
    if ($conn->query("CREATE TABLE IF NOT EXISTS mensajes (
      id int(11) NOT NULL AUTO_INCREMENT,
      de_usuario_id int(11) NOT NULL,
      para_usuario_id int(11) NOT NULL,
      mensaje text NOT NULL,
      fecha_envio timestamp NULL DEFAULT current_timestamp(),
      leido tinyint(1) DEFAULT 0,
      PRIMARY KEY (id),
      KEY de_usuario_id (de_usuario_id),
      KEY para_usuario_id (para_usuario_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;")) {
        echo "<p style='color:blue'>ℹ️ Intenté crear la tabla automáticamente. Recarga para verificar.</p>";
    } else {
        echo "<p style='color:red'>❌ Error al intentar crear la tabla: " . $conn->error . "</p>";
    }
}

echo "<h2>4. Prueba de API (GET simulado)</h2>";
// Simulate a request
$_POST['action'] = 'get';
$_POST['otro_id'] = 999; // ID dummy
// Capture output of chat_api.php
ob_start();
// simulate session if needed for include
if (!isset($_SESSION['usuario_id'])) { $_SESSION['usuario_id'] = 1; } 
include('chat_api.php');
$output = ob_get_clean();

echo "<strong>Salida de chat_api.php:</strong>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";
$json = json_decode($output);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "<p style='color:green'>✅ chat_api.php devuelve JSON válido.</p>";
} else {
    echo "<p style='color:red'>❌ chat_api.php NO devuelve JSON válido. Error: " . json_last_error_msg() . "</p>";
}
?>