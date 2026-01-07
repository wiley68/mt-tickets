<?php
if (!defined('ABSPATH')) exit;

$logo_id = (int) get_option('mt_tickets_footer_logo_id', 0);
$url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
if (!$url) $url = get_theme_file_uri('assets/images/logo-placeholder.svg');

$attrs = get_block_wrapper_attributes(array('class' => 'mt-footer-logo'));

?>
<a <?php echo $attrs; ?> href="<?php echo esc_url(home_url('/')); ?>">
	<img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" style="height:44px;width:auto;">
</a>

