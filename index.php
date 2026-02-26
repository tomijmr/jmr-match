<?php include('db.php'); 



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupido App | Ingresar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ff416c, #ff4b2b); height: 100vh; display: flex; align-items: center; color: white; }
        .card { border-radius: 20px; padding: 20px; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 text-center">
                <h1 class="mb-4">Test JMR MATCH❤️‍🔥</h1>
                <div class="card shadow">
                    <form action="registro.php" method="GET">
                        <p class="fw-bold">Ingresá el código del local</p>
                        <input type="text" name="codigo" class="form-control form-control-lg text-center mb-3" placeholder="Ej: VALENTIN2026" required>
                        <button type="submit" class="btn btn-danger btn-lg w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>