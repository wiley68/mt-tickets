<?php
if (!defined('ABSPATH')) exit;

// Get logo from options (same as footer-logo block)
$logo_id = (int) get_option('mt_tickets_footer_logo_id', 0);
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
if (!$logo_url) {
	$logo_url = get_theme_file_uri('assets/images/logo-placeholder.svg');
}

$attrs = get_block_wrapper_attributes();

?>
<div <?php echo $attrs; ?>>
	<h4><?php echo esc_html__('About the Platform', 'mt-tickets'); ?></h4>

	<?php if ($logo_url) : ?>
		<a href="<?php echo esc_url(home_url('/')); ?>" class="mt-footer-logo">
			<img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" style="height:44px;width:auto;">
		</a>
	<?php endif; ?>

	<p><?php echo esc_html__('Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.', 'mt-tickets'); ?></p>
</div>