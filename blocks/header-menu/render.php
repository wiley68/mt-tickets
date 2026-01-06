<?php
if (!defined('ABSPATH')) exit;

$menu = wp_nav_menu(array(
	'theme_location' => 'mt_tickets_primary',
	'container'      => false,
	'fallback_cb'    => false,
	'echo'           => false,
	'depth'          => 2,
	'menu_class'     => 'mt-primary-menu__list',
	'items_wrap'     => '<ul class="mt-primary-menu__list">%3$s</ul>',
));

if (!$menu) {
	$menu = '<ul class="mt-primary-menu__list"><li><a href="#">Routes</a></li><li><a href="#">Carriers</a></li><li><a href="#">Help</a></li></ul>';
}

$attrs = get_block_wrapper_attributes(array('class' => 'mt-primary-menu'));
?>
<div <?php echo $attrs; ?>>
	<button class="mt-hamburger" type="button" data-mt-open="#mt-panel-menu" aria-label="<?php echo esc_attr__('Open menu', 'mt-tickets'); ?>">☰</button>
	<?php echo $menu; ?>
</div>

<!-- Off-canvas Menu Panel -->
<div class="mt-panel" id="mt-panel-menu" aria-hidden="true">
	<div class="mt-panel__overlay"></div>
	<div class="mt-panel__content" role="dialog" aria-label="<?php echo esc_attr__('Menu', 'mt-tickets'); ?>">
		<div class="mt-panel__header">
			<strong><?php echo esc_html__('Menu', 'mt-tickets'); ?></strong>
			<button class="mt-panel__close" type="button" data-mt-close>✕</button>
		</div>
		<?php echo $menu; ?>
	</div>
</div>