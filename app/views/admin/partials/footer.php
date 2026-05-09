    </main>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/jquery.min.js"></script>
<script>
    (function() {
        const profile = document.getElementById('adminProfileTrigger');
        const dropdown = document.getElementById('adminDropdown');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');

        if (profile && dropdown) {
            profile.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
            });
            document.addEventListener('click', function(e) {
                if (!profile.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('show');
        }

        if (menuBtn && sidebar && overlay) {
            menuBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('show');
            });
            overlay.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                closeSidebar();
            }
        });
    })();
</script>
<script src="<?= BASE_URL ?>/assets/js/admin-member-status-poll.js" defer></script>
</body>
</html>