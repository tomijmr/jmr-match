<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emisor = $_SESSION['usuario_id'];
    $receptor = $_POST['receptor_id'];

    // 1 guarda el like
    $conn->query("INSERT INTO likes (usuario_da_id, usuario_recibe_id) VALUES ($emisor, $receptor)");

    // 2. Verif si hay match 
    $check = $conn->query("SELECT id FROM likes WHERE usuario_da_id = $receptor AND usuario_recibe_id = $emisor");

    if ($check->num_rows > 0) {
        // match
        header("Location: match.php?with=$receptor");
    } else {
        // no match, sigue buscando
        header("Location: pool.php");
    }
}
?>