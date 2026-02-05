<?php
include('db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nombre_bar = $_POST['nombre_bar'];
    $codigo = strtoupper(substr(md5(uniqid()), 0, 8)); // genera cod 8 digitos random

    $conn->query("INSERT INTO admins (email, password) VALUES ('$email', '$pass')");
    $admin_id = $conn->insert_id;
    
    $conn->query("INSERT INTO locales (nombre, codigo_acceso, admin_id) VALUES ('$nombre_bar', '$codigo', $admin_id)");
    header("Location: admin_login.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><title>Registro Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 text-dark">
                <h3 class="text-center">Registrar mi Boliche</h3>
                <form method="POST">
                    <input type="text" name="nombre_bar" placeholder="Nombre del Bar" class="form-control mb-2" required>
                    <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                    <input type="password" name="password" placeholder="Contraseña" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-primary w-100">Crear Cuenta</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>