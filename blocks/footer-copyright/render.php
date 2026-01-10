<?php
if (!defined('ABSPATH')) exit;

$default_copyright = '© MT Tickets. All rights reserved.';
$copyright = get_option('mt_tickets_footer_copyright_text', $default_copyright);

$image_id = (int) get_option('mt_tickets_footer_payment_icons_id', 0);
$image_url = '';

if ($image_id) {
	$image_url = wp_get_attachment_image_url($image_id, 'full');
}

// Fallback to default image if no custom image is set
if (!$image_url) {
	$image_url = get_theme_file_uri('assets/images/icons_payment.png');
}

$attrs = get_block_wrapper_attributes(array('class' => 'mt-footer-copyright'));
?>
<div <?php echo $attrs; ?>>
	<div class="mt-footer-copyright__container">
		<div class="mt-footer-copyright__column mt-footer-copyright__column--left">
			<p class="mt-footer-copyright__text"><?php echo esc_html($copyright); ?></p>
		</div>
		<div class="mt-footer-copyright__column mt-footer-copyright__column--right">
			<?php if ($image_url) : ?>
				<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr__('Payment methods', 'mt-tickets'); ?>" class="mt-footer-copyright__image" />
			<?php endif; ?>
		</div>
	</div>
</div>
