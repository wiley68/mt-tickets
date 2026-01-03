<?php

/**
 * Bus Tickets Block theme functions.
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Load theme textdomain for translations
 */
function mt_tickets_load_theme_textdomain()
{
	load_theme_textdomain(
		'mt-tickets',
		get_template_directory() . '/languages'
	);
}
add_action('after_setup_theme', 'mt_tickets_load_theme_textdomain');

/**
 * Theme settings page (Appearance -> MT Tickets)
 */
add_action('admin_menu', function () {
	add_theme_page(
		__('MT Tickets Settings', 'mt-tickets'),
		__('MT Tickets', 'mt-tickets'),
		'manage_options',
		'mt-tickets-settings',
		'mt_tickets_render_settings_page'
	);
});
