<?php
include('db.php');
$target_id = $_GET['with'];
$res = $conn->query("SELECT * FROM usuarios WHERE id = $target_id");
$p = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Es un Match! ❤️</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #ff416c; height: 100vh; display: flex; align-items: center; color: white; text-align: center; }
        .match-img { width: 150px; height: 150px; border-radius: 50%; border: 5px solid white; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="display-3 fw-bold">¡MATCH! ❤️</h1>
        <p class="lead">A <?php echo $p['nombre']; ?> también le gustas.</p>
        
        <img src="uploads/<?php echo $p['foto1']; ?>" class="match-img mb-4">
        
        <div class="d-grid gap-3 col-10 mx-auto">
            <a href="https://wa.me/<?php echo $p['whatsapp']; ?>" class="btn btn-light btn-lg">Hablar por WhatsApp</a>
            <a href="https://instagram.com/<?php echo $p['instagram']; ?>" target="_blank" class="btn btn-outline-light">Ver Instagram</a>
            <a href="pool.php" class="mt-3 text-white">Seguir buscando</a>
        </div>
    </div>
</body>
</html>