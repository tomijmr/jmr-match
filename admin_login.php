<?php
include('db.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM admins WHERE email = '$email'";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        // veri hash
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El email no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Cupido Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #212529; height: 100vh; display: flex; align-items: center; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow p-4">
                    <h3 class="text-center mb-4">Panel Boliche</h3>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email del boliche</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Entrar al Panel</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">
                        <small>¿No tienes cuenta? <a href="admin_registro.php">Regístrate aquí</a></small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>