(function () {
    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

    function openPanel(id) {
        const panel = qs(id);
        if (!panel) return;
        panel.classList.add('is-open');
        document.documentElement.classList.add('mt-panel-open');
    }

    function closePanels() {
        qsa('.mt-panel.is-open').forEach(p => p.classList.remove('is-open'));
        document.documentElement.classList.remove('mt-panel-open');
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-mt-open]');
        if (btn) {
            e.preventDefault();
            openPanel(btn.getAttribute('data-mt-open'));
            return;
        }

        if (e.target.closest('[data-mt-close]')) {
            e.preventDefault();
            closePanels();
            return;
        }

        if (e.target.classList.contains('mt-panel__overlay')) {
            closePanels();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePanels();
    });
})();
