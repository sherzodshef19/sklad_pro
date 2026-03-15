<?php
require_once 'includes/init.php';

// Fetch stats
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();

$today_income = $pdo->query("SELECT SUM(quantity * price) FROM income WHERE DATE(date) = CURDATE()")->fetchColumn() ?: 0;
$today_outcome = $pdo->query("SELECT SUM(quantity * price) FROM outcome WHERE DATE(date) = CURDATE()")->fetchColumn() ?: 0;

require_once 'includes/header.php';
?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h1 class="h3 fw-bold mb-0">Бош саҳифа</h1>
        <p class="text-muted small mb-0">Тизимдаги умумий ҳолат</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase mb-2">Жами товарлар</div>
                <div class="h3 fw-bold mb-0 text-primary"><?= $total_products ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase mb-2">Жами мижозлар</div>
                <div class="h3 fw-bold mb-0 text-info"><?= $total_customers ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-start border-success border-4">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase mb-2">Бугунги кирим</div>
                <div class="h3 fw-bold mb-0 text-success"><?= number_format($today_income, 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 border-start border-danger border-4">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase mb-2">Бугунги чиқим</div>
                <div class="h3 fw-bold mb-0 text-danger"><?= number_format($today_outcome, 2) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title fw-bold mb-0 text-dark">Сўнгги амалиётлар</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Тур</th>
                        <th>Товар</th>
                        <th>Миқдор</th>
                        <th>Нарх</th>
                        <th>Сана</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        (SELECT 'Кирим' as type, p.name, i.quantity, i.price, i.date 
                         FROM income i JOIN products p ON i.product_id = p.id)
                        UNION ALL
                        (SELECT 'Чиқим' as type, p.name, o.quantity, o.price, o.date 
                         FROM outcome o JOIN products p ON o.product_id = p.id)
                        ORDER BY date DESC LIMIT 10
                    ");
                    while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td class="ps-4">
                            <span class="badge rounded-pill <?= $row['type'] == 'Кирим' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                <?= $row['type'] ?>
                            </span>
                        </td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td class="text-primary fw-bold"><?= number_format($row['price'], 2) ?></td>
                        <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($row['date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
