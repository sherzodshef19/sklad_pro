<?php
require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklad System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --sidebar-bg: #ffffff;
            --main-bg: #f8f9fa;
        }
        body { background-color: var(--main-bg); min-height: 100vh; }
        
        .layout-wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid #dee2e6;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f8f9fa;
        }
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color) !important;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .sidebar-menu {
            flex-grow: 1;
            padding: 1rem 0.75rem;
            list-style: none;
            margin: 0;
        }
        .nav-item { margin-bottom: 0.25rem; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #495057;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }
        .nav-link.active {
            background-color: #e7f1ff;
            color: var(--primary-color);
            font-weight: 700;
        }
        .nav-icon { width: 20px; text-align: center; margin-right: 12px; opacity: 0.7; }
        .nav-link.active .nav-icon { opacity: 1; }

        /* Content Area */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); width: 100%; max-width: 260px; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .mobile-toggle { display: block !important; }
        }
        .mobile-toggle { display: none; position: fixed; top: 1rem; right: 1rem; z-index: 1100; }
    </style>
</head>
<body>
    <?php if ($is_logged_in): ?>
    <button class="btn btn-white shadow-sm border mobile-toggle" type="button" onclick="document.querySelector('.sidebar').classList.toggle('show')">
        ☰
    </button>

    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a class="sidebar-brand" href="index.php">SKLAD.PRO</a>
            </div>
            <ul class="sidebar-menu">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <span class="nav-icon">📊</span> Бош саҳифа
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">
                        <span class="nav-icon">📦</span> Товарлар
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'customers.php' ? 'active' : '' ?>" href="customers.php">
                        <span class="nav-icon">👥</span> Мижозлар
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'income.php' ? 'active' : '' ?>" href="income.php">
                        <span class="nav-icon">📥</span> Кирим
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'outcome.php' ? 'active' : '' ?>" href="outcome.php">
                        <span class="nav-icon">📤</span> Чиқим
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'sales.php' ? 'active' : '' ?>" href="sales.php">
                        <span class="nav-icon">📜</span> Сотувлар
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>" href="settings.php">
                        <span class="nav-icon">⚙️</span> Созламалар
                    </a>
                </li>
                <li class="nav-item mt-4 border-top pt-4">
                    <a class="nav-link text-danger" href="logout.php">
                        <span class="nav-icon">🚪</span> Чиқиш
                    </a>
                </li>
            </ul>
        </aside>
        
        <main class="main-content">
    <?php endif; ?>
