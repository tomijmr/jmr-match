<?php 
include('db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$codigo = $_REQUEST['codigo'] ?? '';

$res = $conn->query("SELECT id, nombre FROM locales WHERE codigo_acceso = '$codigo'");
$local = $res->fetch_assoc();

if (!$local) {
    die("Error: Código de boliche no encontrado. <a href='index.php'>Volver</a>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $ig = $_POST['instagram'];
    $wa = $_POST['whatsapp'];
    $sexo = $_POST['sexo'];
    $interes = $_POST['interes'];
    $local_id = $_POST['local_id'];

    // Valida la carpeta de uploads asi no se rompe en raiz como ayer !! 
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // manejo de fotos sin metadata
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
            echo "Error en la base de datos: " . $conn->error;
        }
    } else {
        echo "Error: No se pudo subir la foto. Revisa los permisos de la carpeta 'uploads'.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | <?php echo $local['nombre']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2 class="text-center">Crear Perfil en <?php echo $local['nombre']; ?></h2>
        <form action="registro.php?codigo=<?php echo $codigo; ?>" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            <input type="hidden" name="local_id" value="<?php echo $local['id']; ?>">
            <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
            
            <label>Tu Nombre:</label>
            <input type="text" name="nombre" class="form-control mb-3" required>

            <label>Instagram (sin @):</label>
            <input type="text" name="instagram" class="form-control mb-3" required>

            <label>WhatsApp:</label>
            <input type="text" name="whatsapp" class="form-control mb-3" placeholder="54911..." required>

            <div class="row">
                <div class="col-6">
                    <label>Tu sexo:</label>
                    <select name="sexo" class="form-select mb-3">
                        <option value="hombre">Hombre</option>
                        <option value="mujer">Mujer</option>
                    </select>
                </div>
                <div class="col-6">
                    <label>Buscas:</label>
                    <select name="interes" class="form-select mb-3">
                        <option value="mujer">Mujeres</option>
                        <option value="hombre">Hombres</option>
                        <option value="todos">Ambos</option>
                    </select>
                </div>
            </div>

            <label>Tu mejor foto:</label>
            <input type="file" name="foto" class="form-control mb-4" accept="image/*" required>

            <button type="submit" class="btn btn-danger w-100">¡Empezar a Matchear!</button>
        </form>
    </div>
</body>
</html>