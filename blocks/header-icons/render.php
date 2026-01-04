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