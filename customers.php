<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];

        $stmt = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
        $stmt->execute([$name, $phone]);
        $_SESSION['message'] = "Мижоз муваффақиятли қўшилди!";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];

        $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $id]);
        $_SESSION['message'] = "Мижоз маълумотлари янгиланди!";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Мижоз ўчирилди!";
    }
    header("Location: customers.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="h3 fw-bold mb-0">Мижозлар</h1>
        <p class="text-muted small">Тизимдаги барча мижозлар</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#addCustomerCollapse">
            <span>+</span> Янги мижоз қўшиш
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert <?= (strpos($message, 'Хато') !== false || strpos($message, 'error') !== false) ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="collapse mb-4" id="addCustomerCollapse">
    <div class="card card-body border-0 shadow-sm p-4">
        <h5 class="fw-bold mb-4">Янги мижоз қўшиш</h5>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Мижоз исми</label>
                    <input type="text" name="name" class="form-control" required placeholder="Фланчиев Писманчи">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Телефон рақами</label>
                    <input type="text" name="phone" class="form-control" required placeholder="+998 (__) ___-__-__">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="add" class="btn btn-primary w-100 fw-bold">Сақлаш</button>
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
                        <th class="ps-4">ID</th>
                        <th>Исм-фамилия</th>
                        <th>Телефон</th>
                        <th>Қўшилган вақти</th>
                        <th class="text-end pe-4">Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $c['id'] ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['phone']) ?></td>
                        <td class="text-muted small"><?= date('d.m.Y', strtotime($c['created_at'])) ?></td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary border-0 edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal" 
                                    data-id="<?= $c['id'] ?>"
                                    data-name="<?= htmlspecialchars($c['name']) ?>"
                                    data-phone="<?= htmlspecialchars($c['phone']) ?>">
                                Таҳрирлаш
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Ростдан ҳам ўчирмоқчимисиз?')">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger border-0">
                                    Ўчириш
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Мижозлар мавжуд эмас</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog border-0">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title fw-bold">Мижозни таҳрирлаш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Мижоз исми</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Телефон рақами</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" required>
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
            document.getElementById('edit_phone').value = this.dataset.phone;
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
