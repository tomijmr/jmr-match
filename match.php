<?php
include('db.php');

// Validar que el parametro exista y sea numero
if (!isset($_GET['with']) || empty($_GET['with'])) {
    header("Location: pool.php");
    exit();
}

$target_id = intval($_GET['with']);

// Usar consulta preparada para evitar errores
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $target_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    header("Location: pool.php"); // Si el usuario no existe, volver
    exit();
}

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
        body { background: #ff416c; height: 100vh; display: flex; align-items: center; color: white; text-align: center; justify-content: center; }
        .match-card { background: rgba(255, 255, 255, 0.2); padding: 2rem; border-radius: 20px; backdrop-filter: blur(10px); }
        .match-img { width: 150px; height: 150px; border-radius: 50%; border: 5px solid white; object-fit: cover; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="match-card d-inline-block">
            <h1 class="display-3 fw-bold mb-3">¡MATCH! ❤️</h1>
            <p class="lead mb-4">A <strong><?php echo htmlspecialchars($p['nombre']); ?></strong> también le gustas.</p>
            
            <img src="uploads/<?php echo htmlspecialchars($p['foto1']); ?>" class="match-img mb-4">
            
            <div class="d-grid gap-3 col-10 mx-auto">
                <!-- Enlace corregido y asegurado -->
                <a href="chat.php?usuario_id=<?php echo $p['id']; ?>" class="btn btn-light btn-lg fw-bold text-danger">💬 Chatear ahora</a>
                <a href="pool.php" class="btn btn-outline-light">Seguir buscando</a>
            </div>
        </div>
    </div>
</body>
</html>