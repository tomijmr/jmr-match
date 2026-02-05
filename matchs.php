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
        body { background-color: #f8f9fa; }
        .match-card { background: white; border-radius: 15px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; }
        .match-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 15px; border: 2px solid #ff4b2b; }
        .match-info { flex-grow: 1; }
        .btn-whatsapp { color: #25D366; font-size: 24px; margin-right: 15px; }
        .btn-instagram { color: #E1306C; font-size: 24px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-danger">Mis Matches 🔥</h2>
        <a href="pool.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <?php if ($res->num_rows > 0): ?>
        <div class="row">
            <?php while($match = $res->fetch_assoc()): ?>
                <?php 
                    $mensaje = "Hola hicimos match en " . $match['nombre_local']; 
                    $link_whatsapp = "https://wa.me/" . $match['whatsapp'] . "?text=" . urlencode($mensaje);
                    $link_instagram = "https://instagram.com/" . $match['instagram'];
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="match-card">
                        <img src="uploads/<?php echo $match['foto1']; ?>" class="match-img" alt="Foto">
                        <div class="match-info">
                            <h5 class="mb-1"><?php echo $match['nombre']; ?></h5>
                            <small class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo $match['nombre_local']; ?></small>
                        </div>
                        <div>
                            <a href="<?php echo $link_whatsapp; ?>" target="_blank" class="btn-whatsapp"><i class="bi bi-whatsapp"></i></a>
                            <a href="<?php echo $link_instagram; ?>" target="_blank" class="btn-instagram"><i class="bi bi-instagram"></i></a>
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
