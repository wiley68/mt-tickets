(function () {
    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

    function openPanel(id) {
        const panel = qs(id);
        if (!panel) return;

        // Force reflow to ensure initial state is applied
        panel.offsetHeight;

        // Add classes to trigger animation
        panel.classList.add('is-open');
        document.documentElement.classList.add('mt-panel-open');
    }

    function closePanels() {
        const openPanels = qsa('.mt-panel.is-open');

        if (openPanels.length === 0) return;

        // Remove classes to trigger closing animation
        openPanels.forEach(p => p.classList.remove('is-open'));
        document.documentElement.classList.remove('mt-panel-open');

        // Wait for animation to complete before hiding
        setTimeout(() => {
            openPanels.forEach(p => {
                if (!p.classList.contains('is-open')) {
                    // Panel is fully closed
                }
            });
        }, 300); // Match CSS transition duration
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

    // Mini Cart Quantity Controls (AJAX, no reload)
    document.addEventListener('click', async function (e) {
        const qtyBtn = e.target.closest('.mt-mini-cart__qty-btn');
        if (!qtyBtn) return;

        e.preventDefault();

        // Prevent multiple clicks
        if (qtyBtn.classList.contains('is-loading')) return;

        const cartItemKey = qtyBtn.getAttribute('data-cart-item-key');
        const action = qtyBtn.getAttribute('data-action');
        const input = qs(`.mt-mini-cart__qty-input[data-cart-item-key="${cartItemKey}"]`);
        const itemEl = qtyBtn.closest('.mt-mini-cart__item');

        if (!input || !cartItemKey) return;

        let currentQty = parseInt(input.value) || 1;

        if (action === 'increase') {
            currentQty += 1;
        } else if (action === 'decrease' && currentQty > 1) {
            currentQty -= 1;
        } else {
            return; // Don't allow quantity below 1
        }

        // Add loading state
        if (itemEl) itemEl.classList.add('is-updating');
        qtyBtn.classList.add('is-loading');
        qtyBtn.disabled = true;
        input.disabled = true;

        const doUpdateDom = () => {
            if (itemEl) itemEl.classList.remove('is-updating');
            qtyBtn.classList.remove('is-loading');
            qtyBtn.disabled = false;
            input.disabled = false;
            recalcMiniCartCounts();
        };

        // Update WooCommerce cart via AJAX
        if (typeof mtTicketsCart !== 'undefined' && mtTicketsCart.ajax_url) {
            try {
                const response = await fetch(mtTicketsCart.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'mt_update_cart_quantity',
                        nonce: mtTicketsCart.nonce,
                        cart_item_key: cartItemKey,
                        quantity: currentQty
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update input value
                    input.value = currentQty;

                    // Update line total: new quantity * unit price
                    if (itemEl) {
                        const unitPrice = parseFloat(itemEl.getAttribute('data-unit-price')) || 0;
                        const newLineTotal = currentQty * unitPrice;
                        itemEl.setAttribute('data-line-total', newLineTotal);
                    }

                    // Recalculate totals and counts
                    recalcMiniCartCounts();
                } else {
                    // Revert on error
                    console.error('Failed to update cart quantity:', data.data?.message || 'Unknown error');
                    input.value = parseInt(input.value) || 1;
                }

                doUpdateDom();
            } catch (err) {
                console.error('Failed to update cart quantity', err);
                // Revert on error
                input.value = parseInt(input.value) || 1;
                doUpdateDom();
            }
        } else {
            // Fallback: just update DOM without AJAX
            input.value = currentQty;

            // Update line total: new quantity * unit price
            if (itemEl) {
                const unitPrice = parseFloat(itemEl.getAttribute('data-unit-price')) || 0;
                const newLineTotal = currentQty * unitPrice;
                itemEl.setAttribute('data-line-total', newLineTotal);
            }

            setTimeout(() => {
                doUpdateDom();
            }, 300);
        }
    });

    // Helpers: format price using stored meta
    function formatPrice(amount, meta) {
        const {
            currencySymbol = '',
            currencyPosition = 'left',
            decimals = 2,
            decimalSep = '.',
            thousandSep = ',',
        } = meta || {};

        const fixed = Number(amount || 0).toFixed(Number(decimals));
        const parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
        const number = parts.join(decimalSep);

        switch (currencyPosition) {
            case 'right':
                return `${number}${currencySymbol}`;
            case 'left_space':
                return `${currencySymbol} ${number}`;
            case 'right_space':
                return `${number} ${currencySymbol}`;
            case 'left':
            default:
                return `${currencySymbol}${number}`;
        }
    }

    function recalcMiniCartTotals() {
        const totalEl = qs('.mt-mini-cart__total-value');
        if (!totalEl) return 0;

        const items = qsa('.mt-mini-cart__item[data-line-total]');
        let total = 0;
        items.forEach((item) => {
            const v = parseFloat(item.getAttribute('data-line-total'));
            if (!isNaN(v)) total += v;
        });

        const meta = {
            currencySymbol: totalEl.dataset.currencySymbol || '',
            currencyPosition: totalEl.dataset.currencyPosition || 'left',
            decimals: totalEl.dataset.decimals || 2,
            decimalSep: totalEl.dataset.decimalSep || '.',
            thousandSep: totalEl.dataset.thousandSep || ',',
        };

        totalEl.textContent = formatPrice(total, meta);
        totalEl.setAttribute('data-total', total);
        return total;
    }

    // Helpers to recompute mini cart counts/totals and UI without reload
    function recalcMiniCartCounts() {
        const qtyInputs = qsa('.mt-mini-cart__qty-input');
        let total = 0;
        qtyInputs.forEach((input) => {
            const v = parseInt(input.value, 10);
            if (!isNaN(v)) total += v;
        });

        const headerCount = qs('.mt-mini-cart__count');
        if (headerCount) {
            if (total > 0) {
                headerCount.textContent = `(${total})`;
                headerCount.style.display = 'inline';
            } else {
                headerCount.style.display = 'none';
            }
        }

        const badge = qs('.mt-cart-badge');
        if (badge) {
            if (total > 0) {
                badge.textContent = total;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }

        const footer = qs('.mt-mini-cart__footer');
        const body = qs('.mt-mini-cart__body');

        // Recalculate totals from remaining items
        const amountTotal = recalcMiniCartTotals();

        if (total === 0 || amountTotal === 0) {
            if (body) {
                body.innerHTML = '<div class="mt-mini-cart__empty">Your cart is empty.</div>';
            }
            if (footer) footer.style.display = 'none';
        } else {
            if (footer) footer.style.display = '';
        }
    }

    // Mini Cart Remove Button (AJAX, no reload)
    document.addEventListener('click', async function (e) {
        const removeBtn = e.target.closest('.mt-mini-cart__remove');
        if (!removeBtn) return;

        e.preventDefault();

        // Prevent multiple clicks
        if (removeBtn.classList.contains('is-loading')) return;

        const cartItemKey = removeBtn.getAttribute('data-cart-item-key');
        if (!cartItemKey) return;

        const itemEl = removeBtn.closest('.mt-mini-cart__item');
        const qtyInput = itemEl ? itemEl.querySelector('.mt-mini-cart__qty-input') : null;
        const qtyToRemove = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

        // Add loading state
        if (itemEl) itemEl.classList.add('is-removing');
        removeBtn.classList.add('is-loading');
        removeBtn.disabled = true;

        const doRemoveDomUpdate = () => {
            if (itemEl) {
                itemEl.classList.remove('is-removing');
                itemEl.remove();
            }
            removeBtn.classList.remove('is-loading');
            removeBtn.disabled = false;
            recalcMiniCartCounts();
        };

        // If Woo AJAX is available, call it; otherwise just update DOM
        if (typeof wc_add_to_cart_params !== 'undefined') {
            try {
                await fetch(
                    wc_add_to_cart_params.wc_ajax_url
                        .toString()
                        .replace('%%endpoint%%', 'remove_from_cart'),
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            cart_item_key: cartItemKey,
                        }),
                    }
                );
                doRemoveDomUpdate();
            } catch (err) {
                console.error('Failed to remove item from cart', err);
                // Remove loading state on error
                if (itemEl) itemEl.classList.remove('is-removing');
                removeBtn.classList.remove('is-loading');
                removeBtn.disabled = false;
            }
        } else {
            // Simulate delay for better UX even without AJAX
            setTimeout(() => {
                doRemoveDomUpdate();
            }, 300);
        }
    });
})();
