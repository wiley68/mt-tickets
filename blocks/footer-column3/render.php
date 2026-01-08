<?php
if (!defined('ABSPATH')) exit;

$menu = wp_nav_menu(array(
	'theme_location' => 'mt_tickets_footer_column3',
	'container'      => false,
	'fallback_cb'    => false,
	'echo'           => false,
	'depth'          => 1,
	'menu_class'     => 'mt-footer-column3-menu__list',
	'items_wrap'     => '<ul class="mt-footer-column3-menu__list">%3$s</ul>',
));

if (!$menu) {
	// Fallback if no menu assigned
	$menu = '<ul class="mt-footer-column3-menu__list"><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li><li><a href="#">Link 3</a></li></ul>';
}

// Get menu name for title
$menu_name = '';
$assigned = has_nav_menu('mt_tickets_footer_column3');
if ($assigned) {
	$locations = get_nav_menu_locations();
	$menu_id = (int) ($locations['mt_tickets_footer_column3'] ?? 0);
	if ($menu_id) {
		$term = get_term($menu_id, 'nav_menu');
		if ($term && !is_wp_error($term)) {
			$menu_name = (string) $term->name;
		}
	}
}

$attrs = get_block_wrapper_attributes(array('class' => 'mt-footer-column3-menu'));
?>
<div <?php echo $attrs; ?>>
	<?php if ($menu_name) : ?>
		<h4 class="mt-footer-column3-menu__title"><?php echo esc_html($menu_name); ?></h4>
	<?php endif; ?>
	<?php echo $menu; ?>
</div>
