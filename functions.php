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

add_action('admin_init', function () {

	// Left topbar icon (select)
	register_setting('mt_tickets_settings', 'mt_tickets_topbar_icon', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_key',
		'default'           => 'phone',
	));

	// Left topbar text
	register_setting('mt_tickets_settings', 'mt_tickets_topbar_text', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => 'For contact: 555 555 555',
	));

	register_setting('mt_tickets_settings', 'mt_tickets_logo_id', array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	));

	// Header user icon (select)
	register_setting('mt_tickets_settings', 'mt_tickets_header_user_icon', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_key',
		'default'           => 'user',
	));

	// Header cart icon (select)
	register_setting('mt_tickets_settings', 'mt_tickets_header_cart_icon', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_key',
		'default'           => 'cart',
	));

	add_settings_section(
		'mt_tickets_header_section',
		__('Header', 'mt-tickets'),
		'__return_false',
		'mt-tickets-settings'
	);

	add_settings_field(
		'mt_tickets_topbar_icon',
		__('Top bar icon', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_topbar_icon', 'phone');
			$options = array(
				'phone' => __('Phone', 'mt-tickets'),
				'email' => __('Email', 'mt-tickets'),
				'info'  => __('Info', 'mt-tickets'),
			);

			echo '<select name="mt_tickets_topbar_icon">';
			foreach ($options as $k => $label) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr($k),
					selected($value, $k, false),
					esc_html($label)
				);
			}
			echo '</select>';
			echo '<p class="description">' . esc_html__('Choose the icon shown in the left side of the top bar.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_topbar_text',
		__('Top bar text', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_topbar_text', 'For contact: 555 555 555');
			echo '<input type="text" class="regular-text" name="mt_tickets_topbar_text" value="' . esc_attr($value) . '" />';
			echo '<p class="description">' . esc_html__('Example: For contact: 555 555 555', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_topbar_menu_hint',
		__('Top Bar Menu', 'mt-tickets'),
		function () {
			$menus_url     = admin_url('nav-menus.php');
			$locations_url = admin_url('nav-menus.php?action=locations');

			$info = mt_tickets_get_topbar_menu_info();

			echo '<p class="description">';
			echo esc_html__('Right side of the top bar is a WordPress menu assigned to the "Top Bar Menu" location.', 'mt-tickets');
			echo '</p>';

			if ($info['assigned']) {
				$name = $info['menu_name'] ?: __('(assigned)', 'mt-tickets');
				echo '<p><strong>' . esc_html__('Current:', 'mt-tickets') . '</strong> ' . esc_html($name) . '</p>';
			} else {
				echo '<p style="color:#b32d2e;"><strong>' . esc_html__('Current:', 'mt-tickets') . '</strong> ' . esc_html__('Not assigned', 'mt-tickets') . '</p>';
			}

			echo '<p>';
			echo '<a class="button button-secondary" href="' . esc_url($menus_url) . '">' . esc_html__('Manage Menus', 'mt-tickets') . '</a> ';
			echo '<a class="button button-secondary" style="margin-left:6px" href="' . esc_url($locations_url) . '">' . esc_html__('Menu Locations', 'mt-tickets') . '</a>';
			echo '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_logo_id',
		__('Header Logo', 'mt-tickets'),
		function () {
			$logo_id = (int) get_option('mt_tickets_logo_id', 0);
			$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
			$placeholder = get_theme_file_uri('assets/images/logo-placeholder.svg');

			echo '<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">';
			echo '<img id="mt_tickets_logo_preview" src="' . esc_url($logo_url ?: $placeholder) . '" style="height:44px;width:auto;border:1px solid #e2e8f0;border-radius:10px;background:#fff;padding:6px;">';
			echo '<input type="hidden" id="mt_tickets_logo_id" name="mt_tickets_logo_id" value="' . esc_attr($logo_id) . '">';
			echo '<button type="button" class="button button-secondary" id="mt_tickets_logo_select">' . esc_html__('Select logo', 'mt-tickets') . '</button>';
			echo '<button type="button" class="button button-secondary" id="mt_tickets_logo_remove">' . esc_html__('Remove', 'mt-tickets') . '</button>';
			echo '<p class="description" style="margin:0;flex-basis:100%;">' . esc_html__('Used in the header bar. If empty, a placeholder logo is shown.', 'mt-tickets') . '</p>';
			echo '</div>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_primary_menu_hint',
		__('Primary Menu', 'mt-tickets'),
		function () {
			$menus_url     = admin_url('nav-menus.php');
			$locations_url = admin_url('nav-menus.php?action=locations');

			$info = mt_tickets_get_primary_menu_info();

			echo '<p class="description">';
			echo esc_html__('The header menu is a WordPress menu assigned to the "Primary Menu" location.', 'mt-tickets');
			echo '</p>';

			if ($info['assigned']) {
				$name = $info['menu_name'] ?: __('(assigned)', 'mt-tickets');
				echo '<p><strong>' . esc_html__('Current:', 'mt-tickets') . '</strong> ' . esc_html($name) . '</p>';
			} else {
				echo '<p style="color:#b32d2e;"><strong>' . esc_html__('Current:', 'mt-tickets') . '</strong> ' . esc_html__('Not assigned', 'mt-tickets') . '</p>';
			}

			echo '<p>';
			echo '<a class="button button-secondary" href="' . esc_url($menus_url) . '">' . esc_html__('Manage Menus', 'mt-tickets') . '</a> ';
			echo '<a class="button button-secondary" style="margin-left:6px" href="' . esc_url($locations_url) . '">' . esc_html__('Menu Locations', 'mt-tickets') . '</a>';
			echo '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_header_user_icon',
		__('Header User Icon', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_header_user_icon', 'user');
			$options = array(
				'user' => __('User', 'mt-tickets'),
				'user-circle' => __('User Circle', 'mt-tickets'),
				'account' => __('Account', 'mt-tickets'),
			);

			echo '<select name="mt_tickets_header_user_icon">';
			foreach ($options as $k => $label) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr($k),
					selected($value, $k, false),
					esc_html($label)
				);
			}
			echo '</select>';
			echo '<p class="description">' . esc_html__('Choose the icon shown for the user/account button in the header.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_header_cart_icon',
		__('Header Cart Icon', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_header_cart_icon', 'cart');
			$options = array(
				'cart' => __('Cart', 'mt-tickets'),
				'shopping-bag' => __('Shopping Bag', 'mt-tickets'),
				'basket' => __('Basket', 'mt-tickets'),
			);

			echo '<select name="mt_tickets_header_cart_icon">';
			foreach ($options as $k => $label) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr($k),
					selected($value, $k, false),
					esc_html($label)
				);
			}
			echo '</select>';
			echo '<p class="description">' . esc_html__('Choose the icon shown for the cart button in the header.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);
});

function mt_tickets_render_settings_page()
{
	if (! current_user_can('manage_options')) return;
?>
	<div class="wrap">
		<h1><?php echo esc_html__('MT Tickets Settings', 'mt-tickets'); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('mt_tickets_settings');
			do_settings_sections('mt-tickets-settings');
			submit_button();
			?>
		</form>
	</div>
<?php
}

function mt_tickets_get_topbar_menu_info()
{
	$assigned = has_nav_menu('mt_tickets_topbar');

	$menu_id = 0;
	$menu_name = '';

	if ($assigned) {
		$locations = get_nav_menu_locations();
		$menu_id = (int) ($locations['mt_tickets_topbar'] ?? 0);

		if ($menu_id) {
			$term = get_term($menu_id, 'nav_menu');
			if ($term && !is_wp_error($term)) {
				$menu_name = (string) $term->name;
			}
		}
	}

	return array(
		'assigned'  => $assigned,
		'menu_id'   => $menu_id,
		'menu_name' => $menu_name,
	);
}

function mt_tickets_get_primary_menu_info()
{
	$assigned = has_nav_menu('mt_tickets_primary');

	$menu_id = 0;
	$menu_name = '';

	if ($assigned) {
		$locations = get_nav_menu_locations();
		$menu_id = (int) ($locations['mt_tickets_primary'] ?? 0);

		if ($menu_id) {
			$term = get_term($menu_id, 'nav_menu');
			if ($term && !is_wp_error($term)) {
				$menu_name = (string) $term->name;
			}
		}
	}

	return array(
		'assigned'  => $assigned,
		'menu_id'   => $menu_id,
		'menu_name' => $menu_name,
	);
}

/**
 * Register dynamic blocks (no JS build needed).
 */
add_action('init', function () {
	$base = __DIR__ . '/blocks';

	foreach (array('topbar-left', 'topbar-menu', 'header-logo', 'header-menu', 'header-icons') as $b) {
		if (is_dir($base . '/' . $b)) {
			register_block_type($base . '/' . $b);
		}
	}
});

/**
 * Block Bindings source: mt-tickets/options
 * Allows binding core blocks (like Paragraph/Heading) to wp_options values.
 */
add_action('init', function () {
	if (! function_exists('register_block_bindings_source')) {
		return; // WP < 6.5
	}

	register_block_bindings_source('mt-tickets/options', array(
		'label'              => __('MT Tickets Options', 'mt-tickets'),
		'get_value_callback' => function (array $source_args) {
			$key = $source_args['key'] ?? '';

			$map = array(
				'topbar_left'  => 'mt_tickets_topbar_left',
				'topbar_right' => 'mt_tickets_topbar_right',
			);

			if (! isset($map[$key])) {
				return null;
			}

			return (string) get_option($map[$key], '');
		},
	));
});

/**
 * Enqueue theme stylesheet (optional but useful for tiny CSS).
 */
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'mt-tickets-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get('Version')
	);
	wp_enqueue_script(
		'mt-tickets-ui',
		get_theme_file_uri('assets/js/mt-tickets-ui.js'),
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	// Localize script for AJAX
	if (class_exists('WooCommerce')) {
		wp_localize_script('mt-tickets-ui', 'mtTicketsCart', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('mt_tickets_cart_nonce'),
		));
	}
});

/**
 * AJAX handler for updating cart item quantity
 */
add_action('wp_ajax_mt_update_cart_quantity', 'mt_update_cart_quantity');
add_action('wp_ajax_nopriv_mt_update_cart_quantity', 'mt_update_cart_quantity');

function mt_update_cart_quantity()
{
	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mt_tickets_cart_nonce')) {
		wp_send_json_error(array('message' => __('Security check failed.', 'mt-tickets')));
		return;
	}

	// Check if WooCommerce is active
	if (!class_exists('WooCommerce') || !function_exists('WC')) {
		wp_send_json_error(array('message' => __('WooCommerce is not active.', 'mt-tickets')));
		return;
	}

	$cart = WC()->cart;
	if (!$cart) {
		wp_send_json_error(array('message' => __('Cart not available.', 'mt-tickets')));
		return;
	}

	// Get parameters
	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
	$quantity      = isset($_POST['quantity']) ? absint($_POST['quantity']) : 0;

	if (empty($cart_item_key)) {
		wp_send_json_error(array('message' => __('Cart item key is required.', 'mt-tickets')));
		return;
	}

	if ($quantity < 1) {
		wp_send_json_error(array('message' => __('Quantity must be at least 1.', 'mt-tickets')));
		return;
	}

	// Update cart item quantity
	$updated = $cart->set_quantity($cart_item_key, $quantity);

	if ($updated) {
		// Calculate totals
		$cart->calculate_totals();

		wp_send_json_success(array(
			'cart_total' => $cart->get_cart_total(),
			'cart_count' => $cart->get_cart_contents_count(),
			'cart_subtotal' => $cart->get_subtotal(),
		));
	} else {
		wp_send_json_error(array('message' => __('Failed to update cart item.', 'mt-tickets')));
	}
}

/**
 * Register a menu location for the top bar (right side).
 */
add_action('after_setup_theme', function () {
	register_nav_menus(array(
		'mt_tickets_topbar'  => __('Top Bar Menu', 'mt-tickets'),
		'mt_tickets_primary' => __('Primary Menu', 'mt-tickets'),
	));
});

add_action('rest_api_init', function () {
	register_rest_route('mt-tickets/v1', '/topbar', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			// Site Editor users
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {

			$icon = get_option('mt_tickets_topbar_icon', 'phone');
			$text = get_option('mt_tickets_topbar_text', 'For contact: 555 555 555');

			$assigned = has_nav_menu('mt_tickets_topbar');

			$items_out = array();
			$menu_name = '';
			if ($assigned) {
				$locations = get_nav_menu_locations();
				$menu_id = $locations['mt_tickets_topbar'] ?? 0;

				if ($menu_id) {
					$term = get_term((int)$menu_id, 'nav_menu');
					if ($term && !is_wp_error($term)) {
						$menu_name = (string) $term->name;
					}
				}
			}

			return array(
				'icon' => $icon,
				'text' => $text,
				'menu' => array(
					'assigned' => (bool) $assigned,
					'name'     => $menu_name,
					'items'    => $items_out,
				),
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/headerbar', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$logo_id = (int) get_option('mt_tickets_logo_id', 0);
			$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
			$placeholder = get_theme_file_uri('assets/images/logo-placeholder.svg');

			$assigned = has_nav_menu('mt_tickets_primary');
			$menu_name = '';
			$items_out = array();

			if ($assigned) {
				$locations = get_nav_menu_locations();
				$menu_id = (int)($locations['mt_tickets_primary'] ?? 0);
				if ($menu_id) {
					$term = get_term((int)$menu_id, 'nav_menu');
					if ($term && !is_wp_error($term)) {
						$menu_name = (string) $term->name;
					}
				}
			}

			return array(
				'logo' => array(
					'url' => $logo_url ?: $placeholder,
				),
				'menu' => array(
					'assigned' => (bool)$assigned,
					'name'     => $menu_name,
					'items'    => $items_out,
				),
			);
		},
	));
});

add_action('admin_enqueue_scripts', function ($hook) {
	if ($hook !== 'appearance_page_mt-tickets-settings') return;

	wp_enqueue_media();

	wp_enqueue_script(
		'mt-tickets-admin-settings',
		get_theme_file_uri('assets/js/admin-settings.js'),
		array('jquery'),
		wp_get_theme()->get('Version'),
		true
	);

	wp_localize_script('mt-tickets-admin-settings', 'MT_TICKETS_ADMIN', array(
		'placeholder' => get_theme_file_uri('assets/images/logo-placeholder.svg'),
	));
});
