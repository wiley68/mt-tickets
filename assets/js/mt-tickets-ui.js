(function () {
    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

    // Helper function to check if we're on cart page
    function isOnCartPage() {
        return window.location.pathname.includes('/cart') ||
            qs('.woocommerce-cart') !== null ||
            qs('.wc-block-cart') !== null ||
            qs('.wp-block-woocommerce-cart') !== null;
    }

    // Helper function to check if mini cart panel is open
    function isMiniCartOpen() {
        const panel = qs('#mt-panel-cart');
        return panel && panel.classList.contains('is-open');
    }

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

                    // If on cart page and mini cart is open, reload page to refresh cart page
                    if (isOnCartPage() && isMiniCartOpen()) {
                        window.location.reload();
                        return; // Exit early, page will reload
                    }

                    // Refresh mini cart to sync with server
                    refreshMiniCart(false);
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

                // If on cart page and mini cart is open, reload page to refresh cart page
                if (isOnCartPage() && isMiniCartOpen()) {
                    window.location.reload();
                    return; // Exit early, page will reload
                }

                // Refresh mini cart to sync with server
                refreshMiniCart(false);
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

    // Function to refresh mini cart content
    async function refreshMiniCart(shouldOpenPanel = false) {
        if (typeof mtTicketsCart === 'undefined' || !mtTicketsCart.ajax_url) {
            return;
        }

        try {
            const response = await fetch(mtTicketsCart.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'mt_refresh_mini_cart',
                    nonce: mtTicketsCart.nonce,
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update badge
                const badge = qs('.mt-cart-badge');
                if (data.data.cart_count > 0) {
                    if (badge) {
                        badge.textContent = data.data.cart_count;
                        badge.style.display = '';
                    } else {
                        // Create badge if it doesn't exist
                        const cartBtn = qs('[data-mt-open="#mt-panel-cart"]');
                        if (cartBtn) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'mt-cart-badge';
                            newBadge.setAttribute('aria-label', 'Cart items count');
                            newBadge.textContent = data.data.cart_count;
                            cartBtn.appendChild(newBadge);
                        }
                    }
                } else {
                    if (badge) {
                        badge.style.display = 'none';
                    }
                }

                // Update panel header count
                const headerCount = qs('.mt-mini-cart__count');
                if (data.data.cart_count > 0) {
                    if (headerCount) {
                        headerCount.textContent = `(${data.data.cart_count})`;
                        headerCount.style.display = 'inline';
                    } else {
                        // Create count if it doesn't exist
                        const headerStrong = qs('.mt-mini-cart__header strong');
                        if (headerStrong) {
                            const newCount = document.createElement('span');
                            newCount.className = 'mt-mini-cart__count';
                            newCount.textContent = `(${data.data.cart_count})`;
                            headerStrong.appendChild(newCount);
                        }
                    }
                } else {
                    if (headerCount) {
                        headerCount.style.display = 'none';
                    }
                }

                // Update cart body
                const cartBody = qs('.mt-mini-cart__body');
                if (cartBody) {
                    cartBody.innerHTML = data.data.cart_items_html;
                }

                // Update footer
                const footer = qs('.mt-mini-cart__footer');
                const totalValue = qs('.mt-mini-cart__total-value');

                if (data.data.cart_count > 0) {
                    if (footer) {
                        footer.style.display = '';
                    } else {
                        // Create footer if it doesn't exist
                        const miniCart = qs('.mt-mini-cart');
                        if (miniCart) {
                            const newFooter = document.createElement('div');
                            newFooter.className = 'mt-mini-cart__footer';
                            newFooter.innerHTML = `
                                <div class="mt-mini-cart__total">
                                    <span class="mt-mini-cart__total-label">Total in cart:</span>
                                    <span class="mt-mini-cart__total-value"
                                        data-total="${data.data.cart_total_num}"
                                        data-currency-symbol="${data.data.currency_symbol}"
                                        data-currency-position="${data.data.currency_position}"
                                        data-decimals="${data.data.decimals}"
                                        data-decimal-sep="${data.data.decimal_sep}"
                                        data-thousand-sep="${data.data.thousand_sep}">${data.data.cart_total}</span>
                                </div>
                                <a href="${mtTicketsCart.cart_url}" class="mt-mini-cart__btn mt-mini-cart__btn--secondary">View cart</a>
                                <a href="${mtTicketsCart.checkout_url}" class="mt-mini-cart__btn mt-mini-cart__btn--primary">Checkout</a>
                            `;
                            miniCart.appendChild(newFooter);
                        }
                    }

                    if (totalValue) {
                        totalValue.innerHTML = data.data.cart_total;
                        totalValue.setAttribute('data-total', data.data.cart_total_num);
                        totalValue.setAttribute('data-currency-symbol', data.data.currency_symbol);
                        totalValue.setAttribute('data-currency-position', data.data.currency_position);
                        totalValue.setAttribute('data-decimals', data.data.decimals);
                        totalValue.setAttribute('data-decimal-sep', data.data.decimal_sep);
                        totalValue.setAttribute('data-thousand-sep', data.data.thousand_sep);
                    }
                } else {
                    if (footer) {
                        footer.style.display = 'none';
                    }
                }

                // Update last known state
                lastCartCount = data.data.cart_count;

                // Open panel if requested (e.g., when adding product)
                if (shouldOpenPanel) {
                    openPanel('#mt-panel-cart');
                } else {
                    // Update state even if not opening
                    const currentState = getCurrentCartState();
                    if (currentState) {
                        lastCartCount = currentState.count;
                        lastCartHash = currentState.hash;
                    }
                }
            }
        } catch (err) {
            console.error('Failed to refresh mini cart', err);
        }
    }

    // Listen for WooCommerce cart events
    // Using both jQuery (if available) and native events
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
            // Refresh mini cart when product is added (from product page or shop page)
            refreshMiniCart(true); // Open panel when adding product
        });

        jQuery(document.body).on('updated_wc_div', function () {
            // Refresh mini cart when cart is updated (e.g., from cart page)
            refreshMiniCart(false);
        });

        jQuery(document.body).on('updated_cart_totals', function () {
            // Refresh mini cart when cart totals are updated (cart page quantity changes)
            refreshMiniCart(false);
        });

        jQuery(document.body).on('wc_fragment_refresh', function () {
            // Refresh mini cart on fragment refresh
            refreshMiniCart(false);
        });
    }

    // Also listen for native "Add to cart" button clicks (for shop/archive pages)
    // This ensures we catch the event even if jQuery events don't fire
    document.addEventListener('click', function (e) {
        // Check if clicked element is an "Add to cart" button
        const addToCartBtn = e.target.closest('a.add_to_cart_button') ||
            e.target.closest('button.add_to_cart_button') ||
            e.target.closest('a[data-product_id]') ||
            e.target.closest('button[data-product_id]') ||
            e.target.closest('.wp-block-button__link[data-product_id]') ||
            e.target.closest('.wc-block-grid__product-add-to-cart-button') ||
            e.target.closest('[class*="add-to-cart"]');

        if (addToCartBtn) {
            // Store initial cart count
            const initialCartCount = lastCartCount !== null ? lastCartCount :
                (qs('.mt-cart-badge') ? parseInt(qs('.mt-cart-badge').textContent) || 0 : 0);

            // Wait for WooCommerce AJAX to complete
            // Check multiple times to catch the update
            let checkCount = 0;
            const maxChecks = 20; // Check for 2 seconds (20 * 100ms)

            const checkInterval = setInterval(() => {
                checkCount++;

                // Check if button state changed (AJAX completed)
                const isAdded = addToCartBtn.classList.contains('added');
                const isNotLoading = !addToCartBtn.classList.contains('loading');

                // Also check if cart count changed
                const currentBadge = qs('.mt-cart-badge');
                const currentCartCount = currentBadge ? parseInt(currentBadge.textContent) || 0 : 0;
                const cartCountChanged = currentCartCount > initialCartCount;

                if ((isAdded || (isNotLoading && cartCountChanged)) || checkCount >= maxChecks) {
                    clearInterval(checkInterval);
                    // Small delay to ensure cart is updated on server
                    setTimeout(() => {
                        refreshMiniCart(true); // Open panel when adding product
                    }, 200);
                }
            }, 100);

            // Clear interval after max time to avoid infinite loop
            setTimeout(() => {
                clearInterval(checkInterval);
            }, 3000);
        }
    }, true); // Use capture phase to catch earlier

    // Track cart state for comparison
    let lastCartCount = null;
    let lastCartHash = null;

    function getCurrentCartState() {
        if (typeof mtTicketsCart === 'undefined') return null;

        // Try to get cart count from various sources
        const badge = qs('.mt-cart-badge');
        const badgeCount = badge ? parseInt(badge.textContent) || 0 : 0;

        return {
            count: badgeCount,
            hash: document.body.getAttribute('data-cart-hash') || ''
        };
    }

    // Function to check if cart has changed
    function checkCartChanges() {
        const currentState = getCurrentCartState();

        if (currentState && (
            lastCartCount !== currentState.count ||
            lastCartHash !== currentState.hash
        )) {
            refreshMiniCart(false);
            lastCartCount = currentState.count;
            lastCartHash = currentState.hash;
        }
    }

    // Intercept WooCommerce AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function (...args) {
        const url = args[0];
        if (typeof url === 'string' && (
            url.includes('wc-ajax') ||
            url.includes('update_cart') ||
            url.includes('remove_from_cart') ||
            url.includes('cart')
        )) {
            return originalFetch.apply(this, args).then(response => {
                // After WooCommerce AJAX completes, refresh mini cart
                setTimeout(() => {
                    refreshMiniCart(false);
                }, 500);
                return response;
            });
        }
        return originalFetch.apply(this, args);
    };

    // Also listen for native events (for cart page updates)
    function setupCartPageListeners() {
        // Listen for any quantity input changes (works with blocks too)
        document.addEventListener('change', function (e) {
            if (e.target.matches('input[type="number"].qty') ||
                e.target.matches('input.qty') ||
                e.target.closest('.wc-block-cart-item__quantity') ||
                e.target.closest('.wp-block-woocommerce-cart-item')) {
                // Wait for WooCommerce AJAX to complete
                setTimeout(() => {
                    refreshMiniCart(false);
                }, 1000);
            }
        }, true); // Use capture phase to catch earlier

        // Listen for remove buttons (works with blocks too)
        document.addEventListener('click', function (e) {
            if (e.target.matches('a.remove') ||
                e.target.closest('a.remove') ||
                e.target.matches('button[aria-label*="Remove"]') ||
                e.target.closest('button[aria-label*="Remove"]')) {
                // Wait for WooCommerce AJAX to complete
                setTimeout(() => {
                    refreshMiniCart(false);
                }, 1000);
            }
        }, true);

        // Use MutationObserver to watch for cart block updates
        // Watch the entire body for cart-related changes
        const observer = new MutationObserver(function (mutations) {
            let shouldRefresh = false;

            mutations.forEach(function (mutation) {
                // Check if cart-related elements changed
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1 && (
                            node.matches && (
                                node.matches('.wc-block-cart') ||
                                node.matches('.wp-block-woocommerce-cart') ||
                                node.matches('.woocommerce-cart-form') ||
                                node.matches('[class*="cart"]')
                            )
                        )) {
                            shouldRefresh = true;
                        }
                    });

                    mutation.removedNodes.forEach(function (node) {
                        if (node.nodeType === 1 && (
                            node.matches && (
                                node.matches('.wc-block-cart-item') ||
                                node.matches('.wp-block-woocommerce-cart-item') ||
                                node.matches('tr.cart_item')
                            )
                        )) {
                            shouldRefresh = true;
                        }
                    });
                }

                // Check for attribute changes in quantity inputs
                if (mutation.type === 'attributes' &&
                    mutation.target.matches &&
                    mutation.target.matches('input[type="number"]')) {
                    shouldRefresh = true;
                }
            });

            if (shouldRefresh) {
                // Debounce to avoid too many refreshes
                clearTimeout(window.mtCartRefreshTimeout);
                window.mtCartRefreshTimeout = setTimeout(() => {
                    refreshMiniCart(false);
                }, 800);
            }
        });

        // Observe the entire document for cart changes
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['value', 'class', 'data-cart-hash']
        });

        // Periodic check as fallback (every 2 seconds when on cart page)
        if (window.location.pathname.includes('/cart') ||
            qs('.wc-block-cart') ||
            qs('.woocommerce-cart')) {
            setInterval(() => {
                checkCartChanges();
            }, 2000);
        }
    }

    // Setup listeners when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupCartPageListeners);
    } else {
        setupCartPageListeners();
    }
})();
