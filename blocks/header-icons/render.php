<?php
if (!defined('ABSPATH')) exit;

$attrs = get_block_wrapper_attributes(array('class' => 'mt-header-icons'));

?>
<div <?php echo $attrs; ?>>
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-account" aria-label="<?php echo esc_attr__('Account', 'mt-tickets'); ?>">👤</button>
	<button class="mt-header-icon-btn" type="button" data-mt-open="#mt-panel-cart" aria-label="<?php echo esc_attr__('Cart', 'mt-tickets'); ?>">🛒</button>
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
		// Theme-only UI. No business logic.
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
			<strong><?php echo esc_html__('Cart', 'mt-tickets'); ?></strong>
			<button class="mt-panel__close" type="button" data-mt-close>✕</button>
		</div>
		<?php
		if (class_exists('WooCommerce')) {
			// UI convenience if WooCommerce exists
			echo do_shortcode('[woocommerce_mini_cart]');
		} else {
			echo '<p>' . esc_html__('Mini cart placeholder (will be provided by the ticketing/commerce plugin).', 'mt-tickets') . '</p>';
		}
		?>
	</div>
</div>