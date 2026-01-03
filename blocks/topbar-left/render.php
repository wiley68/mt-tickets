<?php
if (!defined('ABSPATH')) exit;

$icon = get_option('mt_tickets_topbar_icon', 'phone');
$text = get_option('mt_tickets_topbar_text', 'For contact: 555 555 555');

function mt_tickets_svg_icon($icon)
{
	$common = 'width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"';
	$stroke = 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

	switch ($icon) {
		case 'email':
			return "<svg {$common}><path {$stroke} d=\"M4 6h16v12H4z\"/><path {$stroke} d=\"M4 7l8 6 8-6\"/></svg>";
		case 'info':
			return "<svg {$common}><circle {$stroke} cx=\"12\" cy=\"12\" r=\"10\"/><path {$stroke} d=\"M12 10v6\"/><path {$stroke} d=\"M12 7h.01\"/></svg>";
		case 'phone':
		default:
			return "<svg {$common}><path {$stroke} d=\"M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.57 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.09a2 2 0 0 1 2.11-.45c.8.26 1.64.45 2.5.57A2 2 0 0 1 22 16.92z\"/></svg>";
	}
}

$icon_svg = mt_tickets_svg_icon($icon);

$wrapper_attrs = get_block_wrapper_attributes(array(
	'class' => 'mt-topbar-left'
));

?>
<div <?php echo $wrapper_attrs; ?>>
	<span class="mt-topbar-left__icon" aria-hidden="true"><?php echo $icon_svg; ?></span>
	<span class="mt-topbar-left__text"><?php echo esc_html($text); ?></span>
</div>