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

// Tooltip for account button (changes when logged in)
$account_tooltip = esc_attr__('Open account panel to sign in or manage your account', 'mt-tickets');
if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$display_name = $current_user ? ( $current_user->display_name ?: $current_user->user_login ) : '';
	if ($display_name) {
		$account_tooltip = sprintf(
			/* translators: %s: user display name */
			esc_html__('Logged in as %s', 'mt-tickets'),
			$display_name
		);
	}
}

// Get account page URL
$account_url = '';
if ($has_woo && function_exists('wc_get_page_permalink')) {
	$account_url = wc_get_page_permalink('myaccount');
} else {
	$account_url = admin_url('profile.php');
}

// Check if account registration is enabled in WooCommerce
$allow_registration = false;
if ($has_woo) {
	$allow_registration = get_option('woocommerce_enable_myaccount_registration', 'no') === 'yes';
} else {
	// If WooCommerce is not active, check WordPress registration setting
	$allow_registration = get_option('users_can_register', false);
}

// Privacy policy URL
$privacy_policy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';

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
// Privacy policy URL for registration note
$privacy_policy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : home_url('/privacy-policy/');

?>
<div <?php echo $attrs; ?>>
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-account" data-account-url="<?php echo esc_url($account_url); ?>" data-is-logged-in="<?php echo is_user_logged_in() ? '1' : '0'; ?>" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>" data-tooltip="<?php echo esc_attr($account_tooltip); ?>">
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

<!-- Account Panel -->
<div class="mt-panel mt-panel--account" id="mt-panel-account" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content mt-panel__content--account" role="dialog" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>">
		<div class="mt-mini-account">
			<!-- Header -->
			<div class="mt-mini-account__header">
				<strong class="mt-mini-account__title"><?php echo esc_html__('Sign in', 'mt-tickets'); ?></strong>
				<button class="mt-panel__close" type="button" data-mt-close aria-label="<?php echo esc_attr__('Close account panel', 'mt-tickets'); ?>">✕</button>
			</div>

			<!-- Body -->
			<div class="mt-mini-account__body">
				<?php if (!is_user_logged_in()) : ?>
					<!-- Error message container -->
					<div class="mt-mini-account__error" style="display: none;"></div>
					
					<!-- Sign in form -->
					<form class="mt-mini-account__form mt-mini-account__form--signin" method="post">
						<div class="mt-mini-account__form-group">
							<input type="text" name="log" id="user_login" class="mt-mini-account__input" placeholder="<?php echo esc_attr__('Username or Email Address', 'mt-tickets'); ?>" autocomplete="username" required>
						</div>
						<div class="mt-mini-account__form-group">
							<input type="password" name="pwd" id="user_pass" class="mt-mini-account__input" placeholder="<?php echo esc_attr__('Password', 'mt-tickets'); ?>" autocomplete="current-password" required>
						</div>
						<div class="mt-mini-account__form-row">
							<div class="mt-mini-account__form-col">
								<label class="mt-mini-account__checkbox-label">
									<input type="checkbox" name="rememberme" id="rememberme" value="forever" class="mt-mini-account__checkbox">
									<span><?php echo esc_html__('Remember me', 'mt-tickets'); ?></span>
								</label>
							</div>
							<div class="mt-mini-account__form-col">
								<?php
								$lost_password_url = $has_woo && function_exists('wc_lostpassword_url') 
									? wc_lostpassword_url() 
									: wp_lostpassword_url();
								?>
								<a href="<?php echo esc_url($lost_password_url); ?>" class="mt-mini-account__link"><?php echo esc_html__('Forgot your password?', 'mt-tickets'); ?></a>
							</div>
						</div>
						<div class="mt-mini-account__form-group">
							<button type="submit" class="mt-mini-account__btn mt-mini-account__btn--primary mt-mini-account__login-btn">
								<?php echo esc_html__('Log in', 'mt-tickets'); ?>
							</button>
						</div>
						<?php if ($allow_registration) : ?>
						<div class="mt-mini-account__form-group">
							<button type="button" class="mt-mini-account__btn mt-mini-account__btn--secondary mt-mini-account__switch" data-view="register">
								<?php echo esc_html__('Create an account', 'mt-tickets'); ?>
							</button>
						</div>
						<?php endif; ?>
						<?php wp_nonce_field('mt_account_login', 'mt_account_login_nonce'); ?>
					</form>

					<!-- Register form -->
					<?php if ($allow_registration) : ?>
					<form class="mt-mini-account__form mt-mini-account__form--register" method="post" style="display:none;">
						<div class="mt-mini-account__form-group">
							<input type="email" name="user_email" class="mt-mini-account__input" placeholder="<?php echo esc_attr__('Email address', 'mt-tickets'); ?>" autocomplete="email" required>
						</div>
						<div class="mt-mini-account__form-group">
							<p class="mt-mini-account__privacy">
								<?php echo esc_html__('Your personal data will be used to support your experience on this website, to manage access to your account, and for other purposes described in our', 'mt-tickets'); ?>
								<?php if ($privacy_policy_url) : ?>
									<a href="<?php echo esc_url($privacy_policy_url); ?>" target="_blank" rel="noopener" class="mt-mini-account__link">
										<?php echo esc_html__('privacy policy', 'mt-tickets'); ?>
									</a>.
								<?php else : ?>
									<?php echo esc_html__('privacy policy.', 'mt-tickets'); ?>
								<?php endif; ?>
							</p>
						</div>
						<div class="mt-mini-account__form-group">
							<button type="button" class="mt-mini-account__btn mt-mini-account__btn--primary mt-mini-account__register-btn">
								<?php echo esc_html__('Registration', 'mt-tickets'); ?>
							</button>
						</div>
						<div class="mt-mini-account__form-group">
							<button type="button" class="mt-mini-account__btn mt-mini-account__btn--secondary mt-mini-account__switch" data-view="signin">
								<?php echo esc_html__('Already has an account', 'mt-tickets'); ?>
							</button>
						</div>
					</form>
					<?php endif; ?>
				<?php else : ?>
					<!-- Logged in view (will be implemented later) -->
					<div class="mt-mini-account__logged-in">
						<p><?php echo esc_html__('You are logged in.', 'mt-tickets'); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
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
