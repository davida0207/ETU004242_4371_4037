<footer class="admin-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0 text-muted">© 2025 Modern Bootstrap Admin Template</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 text-muted">Built with Bootstrap 5.3.7</p>
            </div>
        </div>
    </div>
</footer>

<script nonce="<?php echo htmlspecialchars(\Flight::get('csp_nonce') ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.querySelector('[data-sidebar-toggle]');
    const wrapper = document.getElementById('admin-wrapper');

    if (toggleButton && wrapper) {
      const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
      if (isCollapsed) {
        wrapper.classList.add('sidebar-collapsed');
        toggleButton.classList.add('is-active');
      }

      toggleButton.addEventListener('click', () => {
        const isCurrentlyCollapsed = wrapper.classList.contains('sidebar-collapsed');

        if (isCurrentlyCollapsed) {
          wrapper.classList.remove('sidebar-collapsed');
          toggleButton.classList.remove('is-active');
          localStorage.setItem('sidebar-collapsed', 'false');
        } else {
          wrapper.classList.add('sidebar-collapsed');
          toggleButton.classList.add('is-active');
          localStorage.setItem('sidebar-collapsed', 'true');
        }
      });
    }
  });
</script>
