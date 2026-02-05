<?php
$host = "localhost";
$user = "a0011086_match"; 
$pass = "PObitovi56";
$db   = "a0011086_match";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }
session_start();
?>