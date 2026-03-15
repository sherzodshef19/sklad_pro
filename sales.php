<?php
require_once 'includes/init.php';

// Handle Delete Sale
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $sale_id = $_POST['id'];
    try {
        $pdo->beginTransaction();

        // Get all items in this sale to revert stock
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM outcome WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $items = $stmt->fetchAll();

        // Revert stock for each item
        foreach ($items as $item) {
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Delete outcome items (cascade will handle this but let's be explicit)
        $stmt = $pdo->prepare("DELETE FROM outcome WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        // Delete the sale record
        $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
        $stmt->execute([$sale_id]);

        $pdo->commit();
        $_SESSION['message'] = "Сотув ўчирилди ва омбор қолдиқлари тикланди!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Хатолик: " . $e->getMessage();
    }
    header("Location: sales.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Filters from GET
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';
$customer_id = $_GET['customer_id'] ?? '';

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();

// Build dynamic query
$where = [];
$params = [];

if ($date_from) {
    $where[] = "s.date >= ?";
    $params[] = $date_from . " 00:00:00";
}
if ($date_to) {
    $where[] = "s.date <= ?";
    $params[] = $date_to . " 23:59:59";
}
if ($customer_id) {
    $where[] = "s.customer_id = ?";
    $params[] = $customer_id;
}

$sql = "
    SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll();

$grand_total = array_sum(array_column($sales, 'total_amount'));

require_once 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="h3 fw-bold mb-0">Сотувлар тарихи</h1>
        <p class="text-muted small">Барча амалга оширилган сотувлар рўйхати</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'Хатолик') !== false ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Дан</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($date_from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Гача</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($date_to) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Мижоз</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">Барча мижозлар</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $customer_id == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold">Фильтр</button>
                <a href="sales.php" class="btn btn-outline-secondary btn-sm">Тозалаш</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Мижоз</th>
                        <th>Сумма</th>
                        <th>Сана</th>
                        <th class="pe-4 text-end">Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $s): ?>
                    <tr>
                        <td class="ps-4 text-muted small">#<?= $s['id'] ?></td>
                        <td><span class="text-dark fw-medium"><?= $s['customer_name'] ? htmlspecialchars($s['customer_name']) : 'Мижозсиз' ?></span></td>
                        <td class="fw-bold text-primary"><?= number_format($s['total_amount'], 2) ?></td>
                        <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($s['date'])) ?></td>
                        <td class="pe-4 text-end">
                            <a href="check.php?id=<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary border-0 d-inline-flex align-items-center gap-1">
                                🖨️ Чек
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Сотувни ўчирмоқчимисиз? Омбор қолдиқлари тикланади.')">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger border-0">
                                    Ўчириш
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Сотувлар мавжуд эмас</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($sales)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="ps-4" colspan="2">Жами: <?= count($sales) ?> та сотув</td>
                        <td class="text-success"><?= number_format($grand_total, 2) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
