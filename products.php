<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'] ?: 0;
        $purchase_price = $_POST['purchase_price'];
        $selling_price = $_POST['selling_price'];

        $stmt = $pdo->prepare("INSERT INTO products (name, quantity, purchase_price, selling_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $quantity, $purchase_price, $selling_price]);
        $_SESSION['message'] = "Товар муваффақиятли қўшилди!";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $quantity = $_POST['quantity'] ?: 0;
        $purchase_price = $_POST['purchase_price'];
        $selling_price = $_POST['selling_price'];

        $stmt = $pdo->prepare("UPDATE products SET name = ?, quantity = ?, purchase_price = ?, selling_price = ? WHERE id = ?");
        $stmt->execute([$name, $quantity, $purchase_price, $selling_price, $id]);
        $_SESSION['message'] = "Товар маълумотлари янгиланди!";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Товар ўчирилди!";
    }
    header("Location: products.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="h3 fw-bold mb-0">Товарлар</h1>
        <p class="text-muted small">Омбордаги маҳсулотлар рўйхати</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#addFormCollapse">
            <span>+</span> Янги товар қўшиш
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert <?= (strpos($message, 'Хато') !== false || strpos($message, 'error') !== false) ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="collapse mb-4" id="addFormCollapse">
    <div class="card card-body border-0 shadow-sm p-4">
        <h5 class="fw-bold mb-4">Янги товар қўшиш</h5>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Маҳсулот номи</label>
                    <input type="text" name="name" class="form-control" required placeholder="Масалан: Pepsi 1.5L">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Сони (бошланғич)</label>
                    <input type="number" name="quantity" class="form-control" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Келган нархи</label>
                    <input type="number" step="0.01" name="purchase_price" class="form-control" required placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Сотиш нархи</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control" required placeholder="0.00">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="button" class="btn btn-light px-4 me-2" data-bs-toggle="collapse" data-bs-target="#addFormCollapse">Бекор қилиш</button>
                    <button type="submit" name="add" class="btn btn-primary px-5 fw-bold">Қўшиш</button>
                </div>
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
                        <th>ID</th>
                        <th>Номи</th>
                        <th>Сони</th>
                        <th>Келган нархи</th>
                        <th>Сотиш нархи</th>
                        <th class="text-end pe-4">Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $p['id'] ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                        <td>
                            <span class="badge <?= $p['quantity'] <= 5 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?> rounded-pill">
                                <?= $p['quantity'] ?> та
                            </span>
                        </td>
                        <td class="text-muted"><?= number_format($p['purchase_price'], 2) ?></td>
                        <td class="text-primary fw-bold"><?= number_format($p['selling_price'], 2) ?></td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary border-0 edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal" 
                                    data-id="<?= $p['id'] ?>"
                                    data-name="<?= htmlspecialchars($p['name']) ?>"
                                    data-quantity="<?= $p['quantity'] ?>"
                                    data-purchase="<?= $p['purchase_price'] ?>"
                                    data-selling="<?= $p['selling_price'] ?>">
                                Таҳрирлаш
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Ростдан ҳам ўчирмоқчимисиз?')">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger border-0">
                                    Ўчириш
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Товарлар мавжуд эмас</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="ps-4" colspan="2">Жами: <?= count($products) ?> та товар</td>
                        <td>
                            <?= array_sum(array_column($products, 'quantity')) ?> та
                        </td>
                        <td class="text-muted">—</td>
                        <td class="text-primary">
                            <?php
                            $selling_total = 0;
                            foreach ($products as $p) { $selling_total += $p['quantity'] * $p['selling_price']; }
                            echo number_format($selling_total, 2);
                        ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title fw-bold">Товарни таҳрирлаш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small text-muted">Маҳсулот номи</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Сони</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Келган нархи</label>
                            <input type="number" step="0.01" name="purchase_price" id="edit_purchase" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Сотиш нархи</label>
                            <input type="number" step="0.01" name="selling_price" id="edit_selling" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Бекор қилиш</button>
                    <button type="submit" name="update" class="btn btn-primary px-5 fw-bold">Сақлаш</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_quantity').value = this.dataset.quantity;
            document.getElementById('edit_purchase').value = this.dataset.purchase;
            document.getElementById('edit_selling').value = this.dataset.selling;
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
