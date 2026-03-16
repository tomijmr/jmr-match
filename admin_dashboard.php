<?php

include('db.php');

// Si no hay sesión de admin, mandarlo al login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$local = $conn->query("SELECT * FROM locales WHERE admin_id = $admin_id")->fetch_assoc();
$local_id = $local['id'];

// Estadísticas
$total_personas = $conn->query("SELECT count(*) as total FROM usuarios WHERE local_id = $local_id")->fetch_assoc()['total'];
$total_matches = $conn->query("SELECT count(*) as total FROM likes l1 JOIN likes l2 ON l1.usuario_da_id = l2.usuario_recibe_id AND l1.usuario_recibe_id = l2.usuario_da_id WHERE l1.usuario_da_id IN (SELECT id FROM usuarios WHERE local_id = $local_id) AND l1.id < l2.id")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control | <?php echo $local['nombre']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212 !important; color: white !important; }
        .card { background-color: #1E1E1E; border: 1px solid #333; color: white; }
        h1, h2, h3, h4 { color: white; }
        .text-primary { color: #FF751F !important; }
        .border-primary { border-color: #FF751F !important; }
        .btn-outline-danger { color: #FF751F; border-color: #FF751F; }
        .btn-outline-danger:hover { background-color: #FF751F; color: white; }
        .badge.bg-danger { background-color: #FF751F !important; }
        code { color: #FF751F; }
    </style>
</head>
<body class="">
    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8 d-flex align-items-center gap-3">
                <img src="img/logo.png" style="width: 80px; height: auto;">
                <div>
                    <h1 class="mb-0">Panel de <?php echo $local['nombre']; ?></h1>
                    <p class="lead mb-0 text-muted">Gestiona tu evento</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="logout.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>

        <div class="row text-center mb-5">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm border-primary">
                    <h4>Asistentes Registrados</h4>
                    <h2 class="display-4 fw-bold text-primary"><?php echo $total_personas; ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4 shadow-sm border-success">
                    <h4>Matches Generados</h4>
                    <h2 class="display-4 fw-bold text-success"><?php echo $total_matches; ?></h2>
                </div>
            </div>
        </div>

        <div class="card p-5 shadow">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h3>Tu Código de Acceso: <span class="badge bg-danger"><?php echo $local['codigo_acceso']; ?></span></h3>
                    <p class="text-muted mt-3">Instrucciones: Imprime este código o genera un QR que lleve a los usuarios a:</p>
                    <code>https://tu-dominio.com/index.php?codigo=<?php echo $local['codigo_acceso']; ?></code>
                </div>
                <div class="col-md-5 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://tu-dominio.com/index.php?codigo=<?php echo $local['codigo_acceso']; ?>" alt="QR Code">
                    <p class="mt-2"><small>Escanea para probar</small></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>