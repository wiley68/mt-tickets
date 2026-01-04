/**
 * Header Icons Block Frontend JavaScript
 * Handles panel opening/closing and mini cart interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    // Panel functionality
    const panelTriggers = document.querySelectorAll('[data-mt-open]');
    const panelCloses = document.querySelectorAll('[data-mt-close]');
    const html = document.documentElement;

    // Open panel
    panelTriggers.forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            const panelId = this.getAttribute('data-mt-open');
            const panel = document.getElementById(panelId);

            if (panel) {
                panel.classList.add('is-open');
                panel.removeAttribute('aria-hidden');
                panel.removeAttribute('inert');

                // Ensure no elements in the panel have focus when opening
                const focusableElements = panel.querySelectorAll('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
                focusableElements.forEach(element => {
                    if (element === document.activeElement) {
                        element.blur();
                    }
                });

                html.classList.add('mt-panel-open');
            }
        });
    });

    // Close panel
    panelCloses.forEach(closeBtn => {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const panel = this.closest('.mt-panel');

            if (panel) {
                panel.classList.remove('is-open');
                panel.setAttribute('aria-hidden', 'true');
                panel.setAttribute('inert', '');

                // Remove focus from any element inside the panel
                if (panel.contains(document.activeElement)) {
                    document.activeElement.blur();
                }

                html.classList.remove('mt-panel-open');
            }
        });
    });

    // Close panel on overlay click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('mt-panel__overlay')) {
            const panel = e.target.closest('.mt-panel');
            if (panel) {
                panel.classList.remove('is-open');
                panel.setAttribute('aria-hidden', 'true');
                panel.setAttribute('inert', '');

                // Remove focus from any element inside the panel
                if (panel.contains(document.activeElement)) {
                    document.activeElement.blur();
                }

                html.classList.remove('mt-panel-open');
            }
        }
    });

    // Mini cart functionality (only if WooCommerce is active)
    // Check if we're on a page where cart functionality should work
    if (document.querySelector('.mt-header-icons')) {
        // Quantity controls
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('mt-qty-minus') || e.target.classList.contains('mt-qty-plus')) {
                e.preventDefault();

                const cartItem = e.target.closest('.mt-cart-item');
                const cartItemKey = cartItem.getAttribute('data-cart-item-key');
                const qtyInput = cartItem.querySelector('.mt-qty-input');
                let currentQty = parseInt(qtyInput.value);

                if (e.target.classList.contains('mt-qty-minus') && currentQty > 1) {
                    currentQty--;
                } else if (e.target.classList.contains('mt-qty-plus')) {
                    currentQty++;
                }

                // Show loading state
                cartItem.classList.add('loading');
                const qtyButtons = cartItem.querySelectorAll('.mt-qty-btn');
                qtyButtons.forEach(btn => btn.disabled = true);

                // Update quantity via AJAX
                updateCartItemQuantity(cartItemKey, currentQty, qtyInput, cartItem, qtyButtons);
            }
        });

        // Remove item
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('mt-cart-item-remove')) {
                e.preventDefault();

                const cartItem = e.target.closest('.mt-cart-item');
                const cartItemKey = cartItem.getAttribute('data-cart-item-key');

                // Show loading state
                cartItem.classList.add('loading');
                e.target.disabled = true;
                e.target.textContent = 'Removing...';

                // Remove item via AJAX
                removeCartItem(cartItemKey, cartItem, e.target);
            }
        });

        function updateCartItemQuantity(cartItemKey, quantity, qtyInput, cartItem, qtyButtons) {
            qtyInput.value = quantity;
            qtyInput.disabled = true;

            const data = new FormData();
            data.append('action', 'mt_update_cart_item_quantity');
            data.append('cart_item_key', cartItemKey);
            data.append('quantity', quantity);
            data.append('nonce', mt_tickets_ajax.nonce);

            fetch(mt_tickets_ajax.ajax_url, {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    // Remove loading state
                    if (cartItem) cartItem.classList.remove('loading');
                    if (qtyButtons) qtyButtons.forEach(btn => btn.disabled = false);

                    if (data.success) {
                        // Update panel header counter based on cart count from AJAX response
                        const panelHeader = document.querySelector('#mt-panel-cart .mt-panel__header strong');
                        if (panelHeader && data.data && typeof data.data.cart_count !== 'undefined') {
                            // Remove existing counter span if it exists
                            const existingCounter = panelHeader.querySelector('.mt-panel-counter');
                            if (existingCounter) {
                                existingCounter.remove();
                            }

                            // Add new counter if cart is not empty
                            if (data.data.cart_count > 0) {
                                const counterSpan = document.createElement('span');
                                counterSpan.className = 'mt-panel-counter';
                                counterSpan.textContent = `(${data.data.cart_count})`;
                                panelHeader.appendChild(counterSpan);
                            }
                        }

                        // Update cart summary total if it exists
                        const cartSummary = document.querySelector('.mt-cart-total strong');
                        if (cartSummary && data.data && data.data.cart_total) {
                            cartSummary.innerHTML = data.data.cart_total;
                        }

                        // Refresh cart fragments
                        if (typeof wc_cart_fragments_params !== 'undefined') {
                            jQuery(document.body).trigger('wc_fragment_refresh');
                        }
                    } else {
                        qtyInput.value = data.original_quantity || quantity - 1;
                    }
                })
                .catch(error => {
                    console.error('Error updating cart item:', error);
                    qtyInput.value = quantity - 1;
                    // Remove loading state on error
                    if (cartItem) cartItem.classList.remove('loading');
                    if (qtyButtons) qtyButtons.forEach(btn => btn.disabled = false);
                })
                .finally(() => {
                    qtyInput.disabled = false;
                });
        }

        function removeCartItem(cartItemKey, cartItem, removeButton) {
            // Already in loading state from click handler
            // cartItem.style.opacity = '0.5';
            // cartItem.style.pointerEvents = 'none';

            const data = new FormData();
            data.append('action', 'mt_remove_cart_item');
            data.append('cart_item_key', cartItemKey);
            data.append('nonce', mt_tickets_ajax.nonce);

            fetch(mt_tickets_ajax.ajax_url, {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM first
                        cartItem.remove();

                        // Check if cart is now empty and update UI accordingly
                        const cartItems = document.querySelector('.mt-mini-cart-items');
                        const isCartEmpty = !cartItems || cartItems.children.length === 0;

                        if (isCartEmpty) {
                            // Cart is empty, show empty message
                            const miniCart = document.querySelector('.mt-mini-cart');
                            if (miniCart) {
                                miniCart.innerHTML = '<div class="mt-mini-cart-empty"><p>Your cart is empty.</p></div>';
                            }
                        }

                        // Update panel header counter based on actual cart state
                        const panelHeader = document.querySelector('#mt-panel-cart .mt-panel__header strong');
                        if (panelHeader) {
                            // Remove existing counter span if it exists
                            const existingCounter = panelHeader.querySelector('.mt-panel-counter');
                            if (existingCounter) {
                                existingCounter.remove();
                            }

                            // Add new counter if cart is not empty
                            if (!isCartEmpty) {
                                const counterSpan = document.createElement('span');
                                counterSpan.className = 'mt-panel-counter';
                                counterSpan.textContent = `(${cartItems.children.length})`;
                                panelHeader.appendChild(counterSpan);
                            }
                        }

                        // Update cart summary if it exists
                        const cartSummary = document.querySelector('.mt-cart-total strong');
                        if (cartSummary && data.data && data.data.cart_total) {
                            cartSummary.innerHTML = data.data.cart_total;
                        }

                        // Update cart count badge
                        const cartBadge = document.querySelector('.mt-cart-badge');
                        if (cartBadge && data.data && typeof data.data.cart_count !== 'undefined') {
                            if (data.data.cart_count > 0) {
                                cartBadge.textContent = data.data.cart_count;
                                cartBadge.style.display = '';
                            } else {
                                cartBadge.style.display = 'none';
                            }
                        }

                        // Refresh cart fragments
                        if (typeof wc_cart_fragments_params !== 'undefined') {
                            jQuery(document.body).trigger('wc_fragment_refresh');
                        }
                    } else {
                        // Remove loading state on error
                        cartItem.classList.remove('loading');
                        if (removeButton) {
                            removeButton.disabled = false;
                            removeButton.textContent = 'Remove';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error removing cart item:', error);
                    // Remove loading state on error
                    cartItem.classList.remove('loading');
                    if (removeButton) {
                        removeButton.disabled = false;
                        removeButton.textContent = 'Remove';
                    }
                });
        }
    }
});
