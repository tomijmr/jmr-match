<?php
include('db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$mi_id = $_SESSION['usuario_id'];

// Consulta para obtener los matches
// Un match es cuando yo le di like a alguien Y ese alguien me dio like a mí.
$sql = "SELECT u.*, l.nombre as nombre_local 
        FROM usuarios u
        JOIN likes l1 ON l1.usuario_recibe_id = u.id AND l1.usuario_da_id = $mi_id
        JOIN likes l2 ON l2.usuario_da_id = u.id AND l2.usuario_recibe_id = $mi_id
        LEFT JOIN locales l ON u.local_id = l.id";

$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Matches - JMR Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #e0e0e0; }
        .match-card { background: #1E1E1E; border: 1px solid #333; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.5); display: flex; align-items: center; color: white; }
        .match-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 15px; border: 2px solid #FF751F; }
        .match-info { flex-grow: 1; }
        h4, h5 { color: white; margin-bottom: 5px; }
        .text-danger { color: #FF751F !important; }
        .btn-outline-danger { color: #FF751F; border: 1px solid #FF751F; }
        .btn-outline-danger:hover, .btn-outline-danger:active { background-color: #FF751F; color: white; border-color: #FF751F; }
        .btn-danger { background-color: #FF751F; border-color: #FF751F; color: white; }
        .btn-danger:hover { background-color: #e06010; border-color: #e06010; }
        .text-muted { color: #aaa !important; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <img src="img/logo.png" style="height: 50px; width: auto;">
            <h2 class="fw-bold text-danger mb-0">Matches 🔥</h2>
        </div>
        <a href="pool.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <?php if ($res->num_rows > 0): ?>
        <div class="row">
            <?php while($match = $res->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="match-card">
                        <img src="uploads/<?php echo $match['foto1']; ?>" class="match-img" alt="Foto">
                        <div class="match-info">
                            <h5 class="mb-1"><?php echo $match['nombre']; ?></h5>
                            <small class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo $match['nombre_local']; ?></small>
                        </div>
                        <div>
                            <a href="chat.php?usuario_id=<?php echo $match['id']; ?>" class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-chat-dots-fill me-1"></i> Chatear
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center mt-5">
            <div class="mb-3" style="font-size: 50px;">💔</div>
            <h4>Aún no tienes matches</h4>
            <p class="text-muted">Sigue dando likes para encontrar a alguien.</p>
            <a href="pool.php" class="btn btn-danger mt-2">Ir a buscar</a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
