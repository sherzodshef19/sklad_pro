<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO income (product_id, quantity, price) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $quantity, $price]);

        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);

        $pdo->commit();
        $_SESSION['message'] = "Кирим муваффақиятли сақланди!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Хатолик юз берди: " . $e->getMessage();
    }
    header("Location: income.php");
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    try {
        $pdo->beginTransaction();
        
        // Get income details to revert stock
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM income WHERE id = ?");
        $stmt->execute([$id]);
        $income = $stmt->fetch();
        
        if ($income) {
            // Decrease stock
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$income['quantity'], $income['product_id']]);
            
            // Delete record
            $stmt = $pdo->prepare("DELETE FROM income WHERE id = ?");
            $stmt->execute([$id]);
            
            $pdo->commit();
            $_SESSION['message'] = "Кирим амалиёти ўчирилди ва омбордаги товар сони қайта тикланди!";
        } else {
            $pdo->rollBack();
            $_SESSION['message'] = "Хато: Кирим маълумоти топилмади!";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Хатолик юз берди: " . $e->getMessage();
    }
    header("Location: income.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$products = $pdo->query("SELECT id, name FROM products ORDER BY name ASC")->fetchAll();
$incomes = $pdo->query("SELECT i.*, p.name as product_name FROM income i JOIN products p ON i.product_id = p.id ORDER BY i.id DESC LIMIT 20")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3 fw-bold mb-0 text-dark">Кирим</h1>
        <p class="text-muted small">Товарларни омборга қабул қилиш</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert <?= (strpos($message, 'Хато') !== false || strpos($message, 'error') !== false) ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4">Янги кирим амалиёти</h5>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Товарни танланг</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Танланг...</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Миқдор (Сони)</label>
                    <input type="number" name="quantity" class="form-control" required placeholder="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Келиш нархи</label>
                        <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="add" class="btn btn-success w-100 fw-bold">Сақлаш</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title fw-bold mb-0 small text-uppercase text-muted">Сўнгги киримлар</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th>ID</th>
                        <th>Товар</th>
                        <th>Миқдор</th>
                        <th>Нарх</th>
                        <th>Жами</th>
                        <th>Сана</th>
                        <th class="pe-4 text-end">Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incomes as $i): ?>
                    <tr>
                        <td class="ps-4 text-muted small"><?= $i['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($i['product_name']) ?></td>
                        <td><span class="badge bg-success-subtle text-success rounded-pill"><?= $i['quantity'] ?> та</span></td>
                        <td><?= number_format($i['price'], 2) ?></td>
                        <td class="fw-bold text-dark"><?= number_format($i['quantity'] * $i['price'], 2) ?></td>
                        <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($i['date'])) ?></td>
                        <td class="text-end pe-4">
                            <form method="POST" class="d-inline" onsubmit="return confirm('Бу киримни ўчирмоқчимисиз? Ундан қўшилган товар сони ҳам омбордан камаяди.')">
                                <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger border-0">
                                    Ўчириш
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
