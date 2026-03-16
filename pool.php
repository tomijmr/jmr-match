<?php 
include('db.php');

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

$mi_id = $_SESSION['usuario_id'];
$mi_local = $_SESSION['local_id'];

// 1 obt datos del perfil del user 
$yo = $conn->query("SELECT interes, sexo FROM usuarios WHERE id = $mi_id")->fetch_assoc();
$busco = $yo['interes'];

// 2 busca personas sin like o dislike previo segun interes 
$filtro_sexo = ($busco == 'todos') ? "" : "AND sexo = '$busco'";

$sql = "SELECT * FROM usuarios 
        WHERE local_id = $mi_local 
        AND id != $mi_id 
        $filtro_sexo
        AND id NOT IN (SELECT usuario_recibe_id FROM likes WHERE usuario_da_id = $mi_id)
        LIMIT 1";

$res = $conn->query($sql);
$persona = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descubrir - JMR Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #121212; color: #e0e0e0; }
        .tinder-card { border-radius: 20px; overflow: hidden; max-width: 400px; margin: 20px auto; background: #1E1E1E; box-shadow: 0 10px 20px rgba(0,0,0,0.5); border: 1px solid #333; }
        .profile-img { width: 100%; height: 450px; object-fit: cover; }
        .info { padding: 20px; color: white; }
        .info h3 { color: white; }
        .text-muted { color: #adb5bd !important; }
        .actions { display: flex; justify-content: space-around; padding-bottom: 20px; }
        .btn-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; border: none; transition: 0.3s; }
        .btn-like { background: #FF751F; color: white; }
        .btn-like:hover { background: #e06010; }
        .btn-dislike { background: #333; color: #fff; }
        .btn-dislike:hover { background: #444; }
        
        /* Navbar Dark Mode Override */
        .navbar { background-color: #1E1E1E !important; border-bottom: 1px solid #333; }
        .navbar-brand { color: #FF751F !important; }
        .bi-box-arrow-left { color: #ccc; }
        .bi-chat-heart-fill { color: #FF751F; }
        .btn-danger { background-color: #FF751F; border-color: #FF751F; }
    </style>
</head>
<body>

<div class="container">
    <nav class="navbar navbar-light bg-light mb-3 shadow-sm rounded p-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="logout.php" class="text-secondary fs-4"><i class="bi bi-box-arrow-left"></i></a>
            <span class="navbar-brand mb-0 h1 text-danger fw-bold">
                <img src="img/logo3.png" alt="Zepellin" style="height: 40px; width: auto;">
            </span>
            <a href="matchs.php" class="text-danger fs-3 position-relative">
                <i class="bi bi-chat-heart-fill"></i>
            </a>
        </div>
    </nav>

    <?php if ($persona): ?>
    <div class="tinder-card">
        <img src="uploads/<?php echo $persona['foto1']; ?>" class="profile-img">
        <div class="info">
            <h3><?php echo $persona['nombre']; ?>, <?php echo $persona['edad']; ?></h3>
            <p class="text-muted"><i class="bi bi-person-fill"></i> <?php echo $persona['sexo']; ?></p>
        </div>
        <div class="actions">
            <a href="pool.php" class="btn-circle btn-dislike"><i class="bi bi-x-lg"></i></a>
            
            <form action="procesar_like.php" method="POST">
                <input type="hidden" name="receptor_id" value="<?php echo $persona['id']; ?>">
                <button type="submit" class="btn-circle btn-like"><i class="bi bi-heart-fill"></i></button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center mt-5">
        <h3>¡Ups! No hay más personas por ahora.</h3>
        <p>Vuelve a intentar en unos minutos, ¡la noche recién empieza!</p>
        <a href="pool.php" class="btn btn-danger">Actualizar</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>