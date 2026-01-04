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

                // Update quantity via AJAX
                updateCartItemQuantity(cartItemKey, currentQty, qtyInput);
            }
        });

        // Remove item
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('mt-cart-item-remove')) {
                e.preventDefault();

                const cartItem = e.target.closest('.mt-cart-item');
                const cartItemKey = cartItem.getAttribute('data-cart-item-key');

                // Remove item via AJAX
                removeCartItem(cartItemKey, cartItem);
            }
        });

        function updateCartItemQuantity(cartItemKey, quantity, qtyInput) {
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
                    if (data.success) {
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
                })
                .finally(() => {
                    qtyInput.disabled = false;
                });
        }

        function removeCartItem(cartItemKey, cartItem) {
            cartItem.style.opacity = '0.5';
            cartItem.style.pointerEvents = 'none';

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
                        cartItem.style.opacity = '1';
                        cartItem.style.pointerEvents = 'auto';
                    }
                })
                .catch(error => {
                    console.error('Error removing cart item:', error);
                    cartItem.style.opacity = '1';
                    cartItem.style.pointerEvents = 'auto';
                });
        }
    }
});
