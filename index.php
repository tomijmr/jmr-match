<?php 
include('db.php');

// Función para comprimir y redimensionar imágenes
function compressImage($source, $destination, $quality) {
    $imgInfo = getimagesize($source);
    $mime = $imgInfo['mime'];

    switch($mime){
        case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
        case 'image/png':  $image = imagecreatefrompng($source); break;
        case 'image/gif':  $image = imagecreatefromgif($source); break;
        case 'image/webp': $image = imagecreatefromwebp($source); break;
        default: return false;
    }

    // Fix rotación automática de celulares (EXIF)
    if ($mime == 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($source);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: $image = imagerotate($image, 180, 0); break;
                case 6: $image = imagerotate($image, -90, 0); break;
                case 8: $image = imagerotate($image, 90, 0); break;
            }
        }
    }

    // Redimensionar a max 800px de ancho
    $maxWidth = 800;
    $width = imagesx($image);
    $height = imagesy($image);

    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = floor($height * ($maxWidth / $width));
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $tmp;
    }

    // Guardar siempre como JPG comprimido
    imagejpeg($image, $destination, $quality);
    imagedestroy($image);
    return true;
}

// Hardcodeamos el acceso a Zeppelin
$codigo_default = 'ZEPPELIN'; 
$res = $conn->query("SELECT id, nombre FROM locales WHERE codigo_acceso = '$codigo_default'");
$local = $res->fetch_assoc();

if (!$local) {
    die("Error de configuración: Local Zeppelin no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $edad = intval($_POST['edad']);
    $sexo = $_POST['sexo'];
    $interes = 'todos'; // Default
    $local_id = $local['id']; 

    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $foto_nombre = time() . "_" . uniqid() . ".jpg"; // Guardamos siempre como JPG comprimido
    $ruta_destino = "uploads/" . $foto_nombre;

    // Usamos la función de compresión (Calidad 60 para web rápida)
    if (compressImage($_FILES['foto']['tmp_name'], $ruta_destino, 60)) {
        // Insertamos NULL en instagram y whatsapp
        $sql = "INSERT INTO usuarios (local_id, nombre, edad, instagram, whatsapp, foto1, sexo, interes) 
                VALUES ('$local_id', '$nombre', $edad, NULL, NULL, '$foto_nombre', '$sexo', '$interes')";
        
        if ($conn->query($sql)) {
            $_SESSION['usuario_id'] = $conn->insert_id;
            $_SESSION['local_id'] = $local_id;
            header("Location: pool.php");
            exit();
        } else {
            echo "Error DB: " . $conn->error;
        }
    } else {
        echo "Error procesando la imagen. Asegúrate de subir JPG, PNG o WEBP.";
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
        .form-control::placeholder { color: rgb(134, 134, 134) !important; opacity: 1; }
        .btn-danger { background-color: #FF751F; border-color: #FF751F; color: white; font-weight: bold; }
        .btn-danger:hover { background-color: #e06010; border-color: #e06010; }
        h1, h2, label { color: #FF751F; }
        .text-muted { color: #ffffff !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <img src="img/logo4.png" alt="Zepellin Bar" style="width: 150px; height: auto; margin-bottom: 2rem;">
            <p class="lead text-muted">¡Creá tu perfil y conocé gente ahora!</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <form action="index.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-lg">
                    
                    <div class="mb-3">
                        <label class="form-label">Tu Nombre:</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Sofia, Tomás o Tomás y Sofia" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tu Edad:</label>
                        <input type="text" name="edad" class="form-control" placeholder="Ej: 18, 22, 25 o 24 y 25" min="18" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sos:</label>
                        <select name="sexo" class="form-select">
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option value="Trans">Trans</option>
                            <option value="Cross">Cross</option>
                            <option value="Pareja Hetero">Pareja Hetero</option>
                            <option value="Pareja Hombres">Pareja Hombres</option>
                            <option value="Pareja Mujeres">Pareja Mujeres</option>
                            <option value="Pareja Swinger">Pareja Swinger</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Tu Foto (Selfie al instante):</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" capture="user" required>
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
