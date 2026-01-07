<?php
if (!defined('ABSPATH')) exit;

// Get logo from options (same as footer-logo block)
$logo_id = (int) get_option('mt_tickets_footer_logo_id', 0);
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
if (!$logo_url) {
	$logo_url = get_theme_file_uri('assets/images/logo-placeholder.svg');
}

$attrs = get_block_wrapper_attributes();

$default_title = 'About the Platform';
$title = get_option('mt_tickets_footer_column1_title', $default_title);

?>
<div <?php echo $attrs; ?>>
	<h4><?php echo esc_html($title); ?></h4>

	<?php if ($logo_url) : ?>
		<a href="<?php echo esc_url(home_url('/')); ?>" class="mt-footer-logo">
			<img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" style="height:44px;width:auto;">
		</a>
	<?php endif; ?>

	<?php
	$default_description = 'Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.';
	$description = get_option('mt_tickets_footer_description', $default_description);
	?>
	<p><?php echo esc_html($description); ?></p>
</div>