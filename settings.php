<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user['password'] === $current_pass) {
        if ($new_pass === $confirm_pass) {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_pass, $_SESSION['user_id']]);
            $_SESSION['message'] = "Парол муваффақиятли ўзгартирилди!";
        } else {
            $_SESSION['error'] = "Янги пароллар мос келмади!";
        }
    } else {
        $_SESSION['error'] = "Жорий парол хато!";
    }
    header("Location: settings.php");
    exit;
}

$message = '';
$error = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Handle Backup
if (isset($_GET['action']) && $_GET['action'] == 'backup') {
    $tables = ['users', 'products', 'customers', 'income', 'outcome'];
    $content = "-- Sklad System Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT * FROM $table");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content .= "DROP TABLE IF EXISTS `$table`;\n";
        
        foreach ($results as $row) {
            $keys = array_keys($row);
            $values = array_values($row);
            $val_str = implode("', '", array_map('addslashes', $values));
            $content .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES ('$val_str');\n";
        }
        $content .= "\n";
    }

    $filename = "backup_" . date('Y-m-d_H-i-s') . ".sql";
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $content;
    exit;
}

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3 fw-bold mb-0 text-dark">Созламалар</h1>
        <p class="text-muted small">Паролни бошқариш ва тизим бэкапи</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Паролни ўзгартириш</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Жорий парол</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Янги парол</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Тасдиқланг</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="change_password" class="btn btn-primary px-4 fw-bold">Сақлаш</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body p-4 text-center">
                <h5 class="fw-bold mb-3">Маълумотлар базаси бэкапи</h5>
                <p class="text-muted small mb-4">
                    Барча маълумотларни SQL файл сифатида юклаб олиш. Хавфсизлик учун бэкапларни мунтазам қилиб туриш тавсия этилади.
                </p>
                <a href="?action=backup" class="btn btn-outline-info w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span>⬇</span> Бэкапни юклаб олиш
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
