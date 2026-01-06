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

    // Mini Cart Quantity Controls
    document.addEventListener('click', function (e) {
        const qtyBtn = e.target.closest('.mt-mini-cart__qty-btn');
        if (!qtyBtn) return;

        e.preventDefault();
        const cartItemKey = qtyBtn.getAttribute('data-cart-item-key');
        const action = qtyBtn.getAttribute('data-action');
        const input = qs(`.mt-mini-cart__qty-input[data-cart-item-key="${cartItemKey}"]`);
        
        if (!input || !cartItemKey) return;

        let currentQty = parseInt(input.value) || 1;
        
        if (action === 'increase') {
            currentQty += 1;
        } else if (action === 'decrease' && currentQty > 1) {
            currentQty -= 1;
        } else {
            return; // Don't allow quantity below 1
        }

        // Update WooCommerce cart via AJAX
        if (typeof wc_add_to_cart_params !== 'undefined') {
            fetch(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    cart_item_key: cartItemKey,
                    quantity: currentQty
                })
            }).then(() => {
                // Reload page to update cart
                window.location.reload();
            });
        }
    });

    // Mini Cart Remove Button
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.mt-mini-cart__remove');
        if (!removeBtn) return;

        e.preventDefault();
        const cartItemKey = removeBtn.getAttribute('data-cart-item-key');
        
        if (!cartItemKey || !confirm('Are you sure you want to remove this item?')) return;

        // Update WooCommerce cart via AJAX
        if (typeof wc_add_to_cart_params !== 'undefined') {
            fetch(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_cart'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    cart_item_key: cartItemKey
                })
            }).then(() => {
                // Reload page to update cart
                window.location.reload();
            });
        }
    });
})();
