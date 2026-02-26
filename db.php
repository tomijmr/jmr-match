<?php
$host = "localhost";
$user = "a0011086_match"; 
$pass = "PObitovi56";
$db   = "a0011086_match";

// $host = "localhost";
// $user = "root"; 
// $pass = "";
// $db   = "jmr_match";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
