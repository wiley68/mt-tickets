<?php
if (!defined('ABSPATH')) exit;

$wrapper_attrs = get_block_wrapper_attributes(array(
	'class' => 'mt-topbar-menu'
));

$menu = wp_nav_menu(array(
	'theme_location' => 'mt_tickets_topbar',
	'container'      => false,
	'fallback_cb'    => false,
	'echo'           => false,
	'depth'          => 1,
));

if (!$menu) {
	// Fallback if no menu assigned
	$menu = '<ul class="menu"><li><a href="#">Help</a></li><li><a href="#">FAQ</a></li><li><a href="#">Contacts</a></li></ul>';
}

?>
<nav <?php echo $wrapper_attrs; ?> aria-label="<?php echo esc_attr__('Top bar menu', 'mt-tickets'); ?>">
	<?php echo $menu; ?>
</nav>