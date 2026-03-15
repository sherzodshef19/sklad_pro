<?php
session_start();
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID топилмади!");
}

$sale_id = $_GET['id'];

// Fetch sale details
$stmt = $pdo->prepare("
    SELECT s.*, c.name as customer_name, c.phone as customer_phone 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Сотув топилмади!");
}

// Fetch sale items
$stmt = $pdo->prepare("
    SELECT o.*, p.name as product_name 
    FROM outcome o 
    JOIN products p ON o.product_id = p.id 
    WHERE o.sale_id = ?
");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Чек #<?= $sale['id'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; margin: 0; padding: 20px; background: #fff; color: #000; }
        .receipt { width: 80mm; margin: 0 auto; border: 1px solid #eee; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { font-size: 20px; margin: 0; text-transform: uppercase; }
        .info { border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items th { border-bottom: 1px dashed #000; text-align: left; padding: 5px 0; font-size: 12px; }
        .items td { padding: 5px 0; vertical-align: top; }
        .total { border-top: 1px solid #000; padding-top: 10px; font-weight: bold; text-align: right; font-size: 16px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; }
        @media print {
            body { padding: 0; background: none; }
            .receipt { width: 100%; border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>SKLAD.PRO</h1>
            <p>Савдо чеки</p>
        </div>
        
        <div class="info">
            Чек №: <?= $sale['id'] ?><br>
            Сана: <?= date('d.m.Y H:i', strtotime($sale['date'])) ?><br>
            Мижоз: <?= $sale['customer_name'] ?: '---' ?><br>
            <?php if ($sale['customer_phone']): ?>
                Тел: <?= $sale['customer_phone'] ?><br>
            <?php endif; ?>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th width="50%">Товар</th>
                    <th width="15%">Миқ</th>
                    <th width="35%" style="text-align: right;">Жами</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td style="text-align: right;"><?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            ЖАМИ: <?= number_format($sale['total_amount'], 2) ?>
        </div>

        <div class="footer">
            Харидингиз учун раҳмат!<br>
            Тизим кузатуви: Sklad.pro
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
