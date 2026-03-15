<?php
require_once 'includes/init.php';

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $price = $_POST['price'];

    // Get product info
    $stmt = $pdo->prepare("SELECT name, quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $p = $stmt->fetch();

    if ($p && $p['quantity'] >= $quantity) {
        // Add to cart session
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                if ($p['quantity'] >= ($item['quantity'] + $quantity)) {
                    $item['quantity'] += $quantity;
                    $found = true;
                } else {
                    $_SESSION['message'] = "Хато: Омборда етарли товар йўқ!";
                    header("Location: outcome.php");
                    exit;
                }
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'name' => $p['name'],
                'quantity' => $quantity,
                'price' => $price
            ];
        }
    } else {
        $_SESSION['message'] = "Хато: Омборда етарли товар йўқ!";
    }
    header("Location: outcome.php");
    exit;
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // re-index
    }
    header("Location: outcome.php");
    exit;
}

// Handle Clear Cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: outcome.php");
    exit;
}

// Handle Finalize Sale
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalize_sale'])) {
    if (empty($_SESSION['cart'])) {
        $_SESSION['message'] = "Хато: Сават бўш!";
        header("Location: outcome.php");
        exit;
    }

    $customer_id = $_POST['customer_id'] ?: null;
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['quantity'] * $item['price'];
    }

    try {
        $pdo->beginTransaction();

        // 1. Create Sale record
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$customer_id, $total_amount]);
        $sale_id = $pdo->lastInsertId();

        // 2. Add items to outcome and update stock
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO outcome (sale_id, product_id, customer_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$sale_id, $item['product_id'], $customer_id, $item['quantity'], $item['price']]);

            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = []; // Clear cart
        header("Location: check.php?id=" . $sale_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Хатолик юз берди: " . $e->getMessage();
        header("Location: outcome.php");
        exit;
    }
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$products = $pdo->query("SELECT id, name, quantity, selling_price FROM products WHERE quantity > 0 ORDER BY name ASC")->fetchAll();
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();

// Fetch last sales
$sales = $pdo->query("
    SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    ORDER BY s.id DESC LIMIT 20
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3 fw-bold mb-0 text-dark">Янги сотув</h1>
        <p class="text-muted small">Маҳсулотларни рўйхатга қўшинг ва сотувни амалга оширинг</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert <?= (strpos($message, 'Хато') !== false || strpos($message, 'error') !== false) ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Маҳсулот қўшиш</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Товарни танланг</label>
                        <select name="product_id" class="form-select" required id="productSelect">
                            <option value="">Танланг...</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>" data-price="<?= $p['selling_price'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['quantity'] ?> та)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Миқдор</label>
                            <input type="number" name="quantity" class="form-control" required placeholder="0" min="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Нарх</label>
                            <input type="number" step="0.01" name="price" class="form-control" required id="priceInput" placeholder="0.00">
                        </div>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">+ Рўйхатга қўшиш</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Сават (Текуш рўйхат)</h5>
                <div class="d-flex gap-2 align-items-center">
                    <a href="?clear_cart=1" class="btn btn-sm btn-outline-secondary border-0 small" onclick="return confirm('Саватни бўшатмоқчимисиз?')">Тозалаш</a>
                    <span class="badge bg-primary rounded-pill"><?= count($_SESSION['cart']) ?> маҳсулот</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Товар</th>
                                <th>Миқдор</th>
                                <th>Нарх</th>
                                <th>Жами</th>
                                <th class="text-end pe-4">Ўчириш</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $cart_total = 0;
                            foreach ($_SESSION['cart'] as $index => $item): 
                                $subtotal = $item['quantity'] * $item['price'];
                                $cart_total += $subtotal;
                            ?>
                            <tr>
                                <td class="ps-4 fw-semibold"><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?> та</td>
                                <td><?= number_format($item['price'], 2) ?></td>
                                <td class="fw-bold"><?= number_format($subtotal, 2) ?></td>
                                <td class="text-end pe-4">
                                    <a href="?remove=<?= $index ?>" class="btn btn-sm btn-outline-danger border-0">✕</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($_SESSION['cart'])): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Рўйхат бўш</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="ps-4 fw-bold">Умумий жами:</td>
                                <td colspan="2" class="text-primary h5 fw-bold"><?= number_format($cart_total, 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php if (!empty($_SESSION['cart'])): ?>
            <div class="card-footer bg-white p-4 border-top">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label small text-muted">Мижозни танланг <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Мижозни танланг...</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="finalize_sale" class="btn btn-success w-100 py-3 fw-bold shadow-sm">
                        ✅ СОТИШ ВА ЧЕК ЧИҚАРИШ (<?= number_format($cart_total, 2) ?>)
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
    document.getElementById('productSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('priceInput').value = price;
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
