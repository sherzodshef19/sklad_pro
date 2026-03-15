<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Логин ёки парол хато!";
        }
    } else {
        $error = "Барча майдонларни тўлдиринг!";
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кириш - Sklad System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <h3 class="text-center mb-4 text-primary fw-bold">SKLAD.PRO</h3>
                        <h5 class="text-center mb-4 text-muted border-bottom pb-3">Тизимга кириш</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Фойдаланувчи номи</label>
                                <input type="text" name="username" class="form-control" required autofocus placeholder="Логин">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small text-muted">Парол</label>
                                <input type="password" name="password" class="form-control" required placeholder="Парол">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                Кириш
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
