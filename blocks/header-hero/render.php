<?php
if (!defined('ABSPATH')) exit;

$attrs = get_block_wrapper_attributes();

// Get settings from theme options
$default_title = 'Buy a bus ticket quickly and conveniently';
$title = get_option('mt_tickets_header_hero_title', $default_title);

$default_description = 'Search by destination, date and carrier';
$description = get_option('mt_tickets_header_hero_description', $default_description);

$shortcode = get_option('mt_tickets_header_hero_search_shortcode', '');

?>
<div <?php echo $attrs; ?>>
	<div class="wp-block-group has-background" style="background-color:var(--wp--preset--color--primary);padding-top:var(--wp--preset--spacing--xl);padding-bottom:var(--wp--preset--spacing--xl);text-align:center">
		<?php if (!empty($title)) : ?>
			<h1 style="color:#FFFFFF;font-size:40px;text-align:center"><?php echo esc_html($title); ?></h1>
		<?php endif; ?>

		<?php if (!empty($description)) : ?>
			<p style="color:#E6F0FF;font-size:18px;text-align:center"><?php echo esc_html($description); ?></p>
		<?php endif; ?>

		<?php if (!empty($shortcode)) : ?>
			<div class="wp-block-group has-background" style="background-color:#FFFFFF;border-radius:12px;margin-top:var(--wp--preset--spacing--l);padding-top:var(--wp--preset--spacing--m);padding-right:var(--wp--preset--spacing--m);padding-bottom:var(--wp--preset--spacing--m);padding-left:var(--wp--preset--spacing--m);text-align:center;display:inline-block">
				<?php echo do_shortcode($shortcode); ?>
			</div>
		<?php endif; ?>
	</div>
</div>