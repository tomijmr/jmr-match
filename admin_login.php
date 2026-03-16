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
        body { background-color: #121212; height: 100vh; display: flex; align-items: center; color: white; }
        .card { border-radius: 15px; background-color: #1E1E1E; border: 1px solid #333; color: white; }
        .form-control { background-color: #2C2C2C; border: 1px solid #444; color: white; }
        .form-control:focus { background-color: #2C2C2C; color: white; border-color: #FF751F; box-shadow: 0 0 0 0.25rem rgba(255, 117, 31, 0.25); }
        .btn-primary { background-color: #FF751F; border-color: #FF751F; color: white; }
        .btn-primary:hover { background-color: #e06010; border-color: #e06010; }
        h3 { color: #FF751F; }
        .alert-danger { background-color: #3e1b1b; border-color: #5c1e1e; color: #ffadad; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow p-4">
                    <div class="text-center mb-4">
                        <img src="img/logo2.png" style="width: 120px; height: auto;">
                        <h3 class="mt-2">Panel Boliche</h3>
                    </div>
                    
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