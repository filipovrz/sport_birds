document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-sidebar-toggle]').forEach(function (btn) {
        var layout = btn.closest('.sidebar-layout');
        if (!layout) {
            return;
        }
        btn.addEventListener('click', function () {
            var open = layout.classList.toggle('is-nav-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
});
