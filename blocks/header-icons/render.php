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
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-account" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>">
		<?php echo $user_icon_svg; ?>
	</button>

	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-cart" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">
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
<div class="mt-panel" id="mt-panel-cart" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content" role="dialog" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">
		<div class="mt-panel__header">
			<strong>
				<?php echo esc_html__('Cart', 'mt-tickets'); ?>
				<?php if ($cart_count !== null && $cart_count > 0) : ?>
					<span class="mt-panel-counter">(<?php echo (int)$cart_count; ?>)</span>
				<?php endif; ?>
			</strong>
			<button class="mt-panel__close" type="button" data-mt-close>✕</button>
		</div>
		<?php
		if ($has_woo) {
			echo '<div class="mt-mini-cart">';
			// mini cart content
			echo '</div>';
		} else {
			echo '<p>' . esc_html__('Mini cart placeholder (will be provided by the ticketing/commerce plugin).', 'mt-tickets') . '</p>';
		}
		?>
	</div>
</div>