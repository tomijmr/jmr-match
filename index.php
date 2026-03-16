<?php 
include('db.php');

// Hardcodeamos el acceso a Zeppelin
$codigo_default = 'ZEPPELIN'; // Este debe existir en la BD
$res = $conn->query("SELECT id, nombre FROM locales WHERE codigo_acceso = '$codigo_default'");
$local = $res->fetch_assoc();

if (!$local) {
    // Si no existe Zeppelin, fallback a ID 1 o error
    die("Error de configuración: Local Zeppelin no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $ig = $_POST['instagram'];
    $wa = $_POST['whatsapp'];
    $sexo = $_POST['sexo'];
    $interes = $_POST['interes'];
    $local_id = $local['id']; // Usamos el ID del local hardcodeado

    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $filename = $_FILES['foto']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $foto_nombre = time() . "_" . uniqid() . "." . $ext;
    $ruta_destino = "uploads/" . $foto_nombre;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
        $sql = "INSERT INTO usuarios (local_id, nombre, instagram, whatsapp, foto1, sexo, interes) 
                VALUES ('$local_id', '$nombre', '$ig', '$wa', '$foto_nombre', '$sexo', '$interes')";
        
        if ($conn->query($sql)) {
            $_SESSION['usuario_id'] = $conn->insert_id;
            $_SESSION['local_id'] = $local_id;
            header("Location: pool.php");
            exit();
        } else {
            echo "Error DB: " . $conn->error;
        }
    } else {
        echo "Error subiendo foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JMR Match | <?php echo $local['nombre']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .card { background-color: #1E1E1E; border: 1px solid #333; color: white; }
        .form-control, .form-select { background-color: #2C2C2C; border: 1px solid #444; color: white; }
        .form-control:focus, .form-select:focus { background-color: #2C2C2C; border-color: #FF751F; color: white; box-shadow: 0 0 0 0.25rem rgba(255, 117, 31, 0.25); }
        .btn-danger { background-color: #FF751F; border-color: #FF751F; color: white; font-weight: bold; }
        .btn-danger:hover { background-color: #e06010; border-color: #e06010; }
        h1, h2 { color: #FF751F; }
        .text-muted { color: #aaa !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <img src="img/logo2.png" alt="Zepellin Bar" style="width: 150px; height: auto; margin-bottom: 2rem;">
            <p class="lead text-muted">¡Creá tu perfil y conocé gente ahora!</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <form action="index.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-lg">
                    
                    <div class="mb-3">
                        <label class="form-label">Tu Nombre:</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Sofía" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instagram (sin @):</label>
                        <input type="text" name="instagram" class="form-control" placeholder="sofia_g" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">WhatsApp:</label>
                        <input type="tel" name="whatsapp" class="form-control" placeholder="387..." required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Sos:</label>
                            <select name="sexo" class="form-select">
                                <option value="hombre">Hombre</option>
                                <option value="mujer">Mujer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Buscás:</label>
                            <select name="interes" class="form-select">
                                <option value="mujer">Mujeres</option>
                                <option value="hombre">Hombres</option>
                                <option value="todos">Ambos</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tu mejor foto:</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fs-5">¡ENTRAR AL MUNDO MATCH!</button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">¿Sos el dueño? <a href="admin_login.php" class="text-decoration-none" style="color: #FF751F;">Admin Login</a></small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
