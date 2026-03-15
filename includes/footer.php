<?php if (isset($_SESSION['user_id'])): ?>
        </main>
    </div> <!-- End layout-wrapper -->
    <footer class="bg-white border-top py-4 mt-auto" style="margin-left: var(--sidebar-width); transition: all 0.3s;" id="appFooter">
        <div class="container-fluid text-center text-muted small">
            &copy; <?= date('Y') ?> Sklad System. Барча ҳуқуқлар ҳимояланган. Offline Version (Bootstrap).
        </div>
    </footer>
    <script>
        // Adjust footer margin on mobile
        function adjustFooter() {
            const footer = document.getElementById('appFooter');
            if (window.innerWidth < 992) {
                footer.style.marginLeft = '0';
            } else {
                footer.style.marginLeft = 'var(--sidebar-width)';
            }
        }
        window.addEventListener('resize', adjustFooter);
        adjustFooter();
    </script>
    <?php endif; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
