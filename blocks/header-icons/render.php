<?php
if (!defined('ABSPATH')) exit;

$attrs = get_block_wrapper_attributes(array('class' => 'mt-header-icons'));

// Woo badge count (show only if WooCommerce exists)
$has_woo = class_exists('WooCommerce');
$cart_count = null;

if ($has_woo && function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart) {
	$cart_count = (int) WC()->cart->get_cart_contents_count(); // 0..n
}

$user_icon = get_option('mt_tickets_header_user_icon', 'user');
$cart_icon = get_option('mt_tickets_header_cart_icon', 'cart');

function mt_tickets_svg_user($icon)
{
	$common = 'width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"';
	$stroke = 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

	switch ($icon) {
		case 'user-circle':
			return "<svg {$common}><circle {$stroke} cx=\"12\" cy=\"12\" r=\"10\"/><path {$stroke} d=\"M20 21a8 8 0 0 0-16 0\"/><circle {$stroke} cx=\"12\" cy=\"8\" r=\"4\"/></svg>";
		case 'account':
			return "<svg {$common}><path {$stroke} d=\"M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2\"/><circle {$stroke} cx=\"12\" cy=\"7\" r=\"4\"/></svg>";
		case 'user':
		default:
			return "<svg {$common}><path {$stroke} d=\"M20 21a8 8 0 0 0-16 0\"/><circle {$stroke} cx=\"12\" cy=\"8\" r=\"4\"/></svg>";
	}
}

function mt_tickets_svg_cart($icon)
{
	$common = 'width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"';
	$stroke = 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

	switch ($icon) {
		case 'shopping-bag':
			return "<svg {$common}><path {$stroke} d=\"M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z\"/><path {$stroke} d=\"M3 6h18\"/><path {$stroke} d=\"M16 10a4 4 0 0 1-8 0\"/></svg>";
		case 'basket':
			return "<svg {$common}><path {$stroke} d=\"M5 7h14l-1 8H6L5 7Z\"/><path {$stroke} d=\"M9 3v4\"/><path {$stroke} d=\"M15 3v4\"/><circle {$stroke} cx=\"9\" cy=\"20\" r=\"1.5\"/><circle {$stroke} cx=\"15\" cy=\"20\" r=\"1.5\"/></svg>";
		case 'cart':
		default:
			return "<svg {$common}><path {$stroke} d=\"M6 6h15l-2 9H7L6 6Z\"/><path {$stroke} d=\"M6 6 5 3H2\"/><circle {$stroke} cx=\"9\" cy=\"20\" r=\"1.5\" fill=\"currentColor\"/><circle {$stroke} cx=\"18\" cy=\"20\" r=\"1.5\" fill=\"currentColor\"/></svg>";
	}
}

$user_icon_svg = mt_tickets_svg_user($user_icon);
$cart_icon_svg = mt_tickets_svg_cart($cart_icon);
?>
<div <?php echo $attrs; ?>>
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-account" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>" data-tooltip="<?php echo esc_attr__('Open account panel to sign in or manage your account', 'mt-tickets'); ?>">
		<?php echo $user_icon_svg; ?>
	</button>

	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-cart" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>" data-tooltip="<?php echo esc_attr__('Open shopping cart to view your selected items', 'mt-tickets'); ?>">
		<?php echo $cart_icon_svg; ?>

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
<div class="mt-panel mt-panel--cart" id="mt-panel-cart" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content mt-panel__content--cart" role="dialog" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">
		<div class="mt-mini-cart">
			<!-- Header -->
			<div class="mt-mini-cart__header">
				<strong>
					<?php echo esc_html__('Your cart', 'mt-tickets'); ?>
					<?php if ($cart_count !== null && $cart_count > 0) : ?>
						<span class="mt-mini-cart__count">(<?php echo (int)$cart_count; ?>)</span>
					<?php endif; ?>
				</strong>
				<button class="mt-panel__close" type="button" data-mt-close aria-label="<?php echo esc_attr__('Close cart', 'mt-tickets'); ?>">✕</button>
			</div>

			<!-- Body -->
			<div class="mt-mini-cart__body">
				<?php
				if ($has_woo && $cart_count > 0) {
					$cart = WC()->cart;
					$cart_totals = $cart->get_totals();
					$cart_total_num = isset($cart_totals['total']) ? (float)$cart_totals['total'] : 0;
					$currency_symbol = get_woocommerce_currency_symbol();
					$currency_pos    = get_option('woocommerce_currency_pos', 'left');
					$decimals        = wc_get_price_decimals();
					$decimal_sep     = wc_get_price_decimal_separator();
					$thousand_sep    = wc_get_price_thousand_separator();
					$cart_items = $cart->get_cart();

					foreach ($cart_items as $cart_item_key => $cart_item) {
						$_product = $cart_item['data'];
						$product_id = $cart_item['product_id'];
						$quantity = $cart_item['quantity'];
						$product_permalink = $_product->get_permalink();
						$product_name = $_product->get_name();
						$product_price = $_product->get_price_html();
						$product_image = $_product->get_image(array(120, 120));
						$line_total = isset($cart_item['line_total']) ? (float)$cart_item['line_total'] : 0;
						$unit_price = $quantity > 0 ? $line_total / $quantity : 0;

				?>
						<div class="mt-mini-cart__item" data-line-total="<?php echo esc_attr($line_total); ?>" data-unit-price="<?php echo esc_attr($unit_price); ?>">
							<div class="mt-mini-cart__item-image">
								<?php echo $product_image; ?>
							</div>
							<div class="mt-mini-cart__item-details">
								<div class="mt-mini-cart__item-name">
									<a href="<?php echo esc_url($product_permalink); ?>">
										<?php echo esc_html($product_name); ?>
									</a>
								</div>
								<div class="mt-mini-cart__item-price"><?php echo $product_price; ?></div>
								<div class="mt-mini-cart__item-actions">
									<div class="mt-mini-cart__quantity">
										<button type="button" class="mt-mini-cart__qty-btn" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" data-action="decrease">−</button>
										<input type="number" class="mt-mini-cart__qty-input" value="<?php echo esc_attr($quantity); ?>" min="1" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" readonly>
										<button type="button" class="mt-mini-cart__qty-btn" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" data-action="increase">+</button>
									</div>
									<button type="button" class="mt-mini-cart__remove" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php echo esc_attr__('Remove item', 'mt-tickets'); ?>">
										<?php echo esc_html__('Remove', 'mt-tickets'); ?>
									</button>
								</div>
							</div>
						</div>
				<?php
					}
				} elseif ($has_woo && $cart_count === 0) {
					echo '<div class="mt-mini-cart__empty">' . esc_html__('Your cart is empty.', 'mt-tickets') . '</div>';
				} else {
					echo '<div class="mt-mini-cart__empty">' . esc_html__('Mini cart placeholder (will be provided by the ticketing/commerce plugin).', 'mt-tickets') . '</div>';
				}
				?>
			</div>

			<!-- Footer -->
			<?php if ($has_woo && $cart_count > 0) : ?>
				<div class="mt-mini-cart__footer">
					<div class="mt-mini-cart__total">
						<span class="mt-mini-cart__total-label"><?php echo esc_html__('Total in cart:', 'mt-tickets'); ?></span>
						<span
							class="mt-mini-cart__total-value"
							data-total="<?php echo esc_attr($cart_total_num); ?>"
							data-currency-symbol="<?php echo esc_attr($currency_symbol); ?>"
							data-currency-position="<?php echo esc_attr($currency_pos); ?>"
							data-decimals="<?php echo esc_attr($decimals); ?>"
							data-decimal-sep="<?php echo esc_attr($decimal_sep); ?>"
							data-thousand-sep="<?php echo esc_attr($thousand_sep); ?>"><?php echo wp_kses_post(wc_price($cart_total_num)); ?></span>
					</div>
					<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="mt-mini-cart__btn mt-mini-cart__btn--secondary">
						<?php echo esc_html__('View cart', 'mt-tickets'); ?>
					</a>
					<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="mt-mini-cart__btn mt-mini-cart__btn--primary">
						<?php echo esc_html__('Checkout', 'mt-tickets'); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
