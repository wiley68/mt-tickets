<?php
if (!defined('ABSPATH')) exit;

$attrs = get_block_wrapper_attributes(array('class' => 'mt-header-icons'));

// Woo badge count (show only if WooCommerce exists)
$has_woo = class_exists('WooCommerce');
$cart_count = null;

if ($has_woo && function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart) {
	$cart_count = (int) WC()->cart->get_cart_contents_count(); // 0..n
}

function mt_tickets_svg_user()
{
	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
		<path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
		<circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
	</svg>';
}

function mt_tickets_svg_cart()
{
	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
		<path d="M6 6h15l-2 9H7L6 6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
		<path d="M6 6 5 3H2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
		<circle cx="9" cy="20" r="1.5" fill="currentColor"/>
		<circle cx="18" cy="20" r="1.5" fill="currentColor"/>
	</svg>';
}
?>
<div <?php echo $attrs; ?>>
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-account" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>">
		<?php echo mt_tickets_svg_user(); ?>
	</button>

	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-cart" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">
		<?php echo mt_tickets_svg_cart(); ?>

		<?php if ($cart_count !== null && $cart_count > 0) : ?>
			<span class="mt-cart-badge" aria-label="<?php echo esc_attr__('Cart items count', 'mt-tickets'); ?>">
				<?php echo (int) $cart_count; ?>
			</span>
		<?php endif; ?>
	</button>
</div>

<!-- Account Panel (UI placeholder) -->
<div class="mt-panel" id="mt-panel-account" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content" role="dialog" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>">
		<div class="mt-panel__header">
			<strong><?php echo esc_html__('Sign in', 'mt-tickets'); ?></strong>
			<button class="mt-panel__close" type="button" data-mt-close>✕</button>
		</div>
		<?php
		if (is_user_logged_in()) {
			echo '<p>' . esc_html__('You are logged in.', 'mt-tickets') . '</p>';
		} else {
			wp_login_form(array('echo' => true));
			echo '<p style="margin-top:12px;"><a href="' . esc_url(wp_registration_url()) . '">' . esc_html__('Create an account', 'mt-tickets') . '</a></p>';
		}
		?>
	</div>
</div>

<!-- Cart Panel (UI placeholder / WooCommerce optional) -->
<div class="mt-panel" id="mt-panel-cart" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content" role="dialog" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">
		<div class="mt-panel__header">
			<strong>
				<?php echo esc_html__('Your cart', 'mt-tickets'); ?>
				<?php if ($cart_count !== null && $cart_count > 0) : ?>
					<span class="mt-panel-counter">(<?php echo (int)$cart_count; ?>)</span>
				<?php endif; ?>
			</strong>
			<button class="mt-panel__close" type="button" data-mt-close>✕</button>
		</div>
		<?php
		if ($has_woo && function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart) {
			$cart = WC()->cart;
			$cart_items = $cart->get_cart();
			$cart_total = $cart->get_cart_total();
		?>
			<div class="mt-mini-cart">
				<?php if (empty($cart_items)) : ?>
					<div class="mt-mini-cart-empty">
						<p><?php echo esc_html__('Your cart is empty.', 'mt-tickets'); ?></p>
					</div>
				<?php else : ?>
					<div class="mt-mini-cart-items">
						<?php foreach ($cart_items as $cart_item_key => $cart_item) :
							$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
							$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

							if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
								$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
								$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
								$product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
								$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
						?>
								<div class="mt-cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
									<div class="mt-cart-item-image">
										<?php if (!empty($product_permalink)) : ?>
											<a href="<?php echo esc_url($product_permalink); ?>">
												<?php echo $thumbnail; ?>
											</a>
										<?php else : ?>
											<?php echo $thumbnail; ?>
										<?php endif; ?>
									</div>
									<div class="mt-cart-item-info">
										<div class="mt-cart-item-name">
											<?php if (!empty($product_permalink)) : ?>
												<a href="<?php echo esc_url($product_permalink); ?>">
													<?php echo wp_kses_post($product_name); ?>
												</a>
											<?php else : ?>
												<?php echo wp_kses_post($product_name); ?>
											<?php endif; ?>
										</div>
										<div class="mt-cart-item-price">
											<?php echo wp_kses_post($product_price); ?>
										</div>
										<div class="mt-cart-item-controls">
											<div class="mt-cart-item-quantity">
												<button class="mt-qty-btn mt-qty-minus" type="button" aria-label="<?php echo esc_attr__('Decrease quantity', 'mt-tickets'); ?>">-</button>
												<input type="number" class="mt-qty-input" value="<?php echo esc_attr($cart_item['quantity']); ?>" min="1" max="<?php echo esc_attr($_product->get_max_purchase_quantity()); ?>" step="1" readonly>
												<button class="mt-qty-btn mt-qty-plus" type="button" aria-label="<?php echo esc_attr__('Increase quantity', 'mt-tickets'); ?>">+</button>
											</div>
											<button class="mt-cart-item-remove" type="button" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php echo esc_attr__('Remove item', 'mt-tickets'); ?>">
												<?php echo esc_html__('Remove', 'mt-tickets'); ?>
											</button>
										</div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>

					<div class="mt-cart-summary">
						<div class="mt-cart-total">
							<strong><?php echo esc_html__('Total:', 'mt-tickets'); ?> <?php echo wp_kses_post($cart_total); ?></strong>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="mt-mini-cart-footer">
				<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="mt-btn mt-btn-secondary">
					<?php echo esc_html__('View cart', 'mt-tickets'); ?>
				</a>
				<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="mt-btn mt-btn-primary">
					<?php echo esc_html__('Checkout', 'mt-tickets'); ?>
				</a>
			</div>
		<?php
		} else {
			echo '<p>' . esc_html__('Mini cart placeholder (will be provided by the ticketing/commerce plugin).', 'mt-tickets') . '</p>';
		}
		?>
	</div>
</div>