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
 * Theme settings pages (Appearance -> MT Tickets)
 */
add_action('admin_menu', function () {
	// Main menu page (parent) - Overview will be the default page
	$parent_slug = 'mt-tickets-overview';

	add_menu_page(
		__('MT Tickets', 'mt-tickets'),
		__('MT Tickets', 'mt-tickets'),
		'manage_options',
		$parent_slug,
		'mt_tickets_render_overview_page',
		'dashicons-admin-generic',
		30
	);

	// Overview submenu (default page)
	add_submenu_page(
		$parent_slug,
		__('Overview', 'mt-tickets'),
		__('Overview', 'mt-tickets'),
		'manage_options',
		'mt-tickets-overview',
		'mt_tickets_render_overview_page'
	);

	// Settings submenu
	add_submenu_page(
		$parent_slug,
		__('Settings', 'mt-tickets'),
		__('Settings', 'mt-tickets'),
		'manage_options',
		'mt-tickets-settings-page',
		'mt_tickets_render_settings_page'
	);

	// Header submenu
	add_submenu_page(
		$parent_slug,
		__('Header Settings', 'mt-tickets'),
		__('Header', 'mt-tickets'),
		'manage_options',
		'mt-tickets-settings',
		'mt_tickets_render_header_settings_page'
	);

	// Footer submenu
	add_submenu_page(
		$parent_slug,
		__('Footer Settings', 'mt-tickets'),
		__('Footer', 'mt-tickets'),
		'manage_options',
		'mt-tickets-footer-settings',
		'mt_tickets_render_footer_settings_page'
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

	// Header Hero title
	register_setting('mt_tickets_settings', 'mt_tickets_header_hero_title', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => 'Buy a bus ticket quickly and conveniently',
	));

	// Header Hero description
	register_setting('mt_tickets_settings', 'mt_tickets_header_hero_description', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default'           => 'Search by destination, date and carrier',
	));

	// Header Hero search shortcode
	register_setting('mt_tickets_settings', 'mt_tickets_header_hero_search_shortcode', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '',
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

	add_settings_field(
		'mt_tickets_header_hero_title',
		__('Header Hero Title', 'mt-tickets'),
		function () {
			$default = 'Buy a bus ticket quickly and conveniently';
			$value = get_option('mt_tickets_header_hero_title', $default);
			echo '<input type="text" name="mt_tickets_header_hero_title" value="' . esc_attr($value) . '" class="regular-text" />';
			echo '<p class="description">' . esc_html__('The main title displayed in the header hero section.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_header_hero_description',
		__('Header Hero Description', 'mt-tickets'),
		function () {
			$default = 'Search by destination, date and carrier';
			$value = get_option('mt_tickets_header_hero_description', $default);
			echo '<textarea name="mt_tickets_header_hero_description" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
			echo '<p class="description">' . esc_html__('The descriptive text displayed below the title in the header hero section.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	add_settings_field(
		'mt_tickets_header_hero_search_shortcode',
		__('Header Hero Search Shortcode', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_header_hero_search_shortcode', '');
			echo '<input type="text" name="mt_tickets_header_hero_search_shortcode" value="' . esc_attr($value) . '" class="regular-text" placeholder="[shortcode_name]" />';
			echo '<p class="description">' . esc_html__('Enter the shortcode identifier for the search form (e.g., [ticket_search]). Leave empty to hide the search section.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings',
		'mt_tickets_header_section'
	);

	// Footer settings registration
	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_column1_title', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => 'About the Platform',
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_logo_id', array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_description', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default'           => 'Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.',
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_column4_title', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => 'For Contact',
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_column4_description', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default'           => "Address:\nPhone:\nEmail:\nOpening hours:",
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_copyright_text', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '© MT Tickets. All rights reserved.',
	));

	register_setting('mt_tickets_footer_settings', 'mt_tickets_footer_payment_icons_id', array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	));

	add_settings_section(
		'mt_tickets_footer_section',
		__('Footer', 'mt-tickets'),
		'__return_false',
		'mt-tickets-footer-settings'
	);

	add_settings_field(
		'mt_tickets_footer_column1_title',
		__('Footer Column 1 Title', 'mt-tickets'),
		function () {
			$default = 'About the Platform';
			$value = get_option('mt_tickets_footer_column1_title', $default);
			echo '<input type="text" name="mt_tickets_footer_column1_title" value="' . esc_attr($value) . '" class="regular-text">';
			echo '<p class="description">' . esc_html__('Title displayed at the top of the footer first column.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_logo_id',
		__('Footer Logo', 'mt-tickets'),
		function () {
			$logo_id = (int) get_option('mt_tickets_footer_logo_id', 0);
			$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
			$placeholder = get_theme_file_uri('assets/images/logo-placeholder.svg');

			echo '<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">';
			echo '<img id="mt_tickets_footer_logo_preview" src="' . esc_url($logo_url ?: $placeholder) . '" style="height:44px;width:auto;border:1px solid #e2e8f0;border-radius:10px;background:#fff;padding:6px;">';
			echo '<input type="hidden" id="mt_tickets_footer_logo_id" name="mt_tickets_footer_logo_id" value="' . esc_attr($logo_id) . '">';
			echo '<button type="button" class="button button-secondary" id="mt_tickets_footer_logo_select">' . esc_html__('Select logo', 'mt-tickets') . '</button>';
			echo '<button type="button" class="button button-secondary" id="mt_tickets_footer_logo_remove">' . esc_html__('Remove', 'mt-tickets') . '</button>';
			echo '<p class="description" style="margin:0;flex-basis:100%;">' . esc_html__('Logo displayed in the footer first column.', 'mt-tickets') . '</p>';
			echo '</div>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_description',
		__('Footer Description', 'mt-tickets'),
		function () {
			$default = 'Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.';
			$value = get_option('mt_tickets_footer_description', $default);
			echo '<textarea name="mt_tickets_footer_description" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
			echo '<p class="description">' . esc_html__('Short description text displayed below the logo in the footer first column.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_column2_menu_hint',
		__('Footer Column 2 Menu', 'mt-tickets'),
		function () {
			$menus_url     = admin_url('nav-menus.php');
			$locations_url = admin_url('nav-menus.php?action=locations');

			$info = mt_tickets_get_footer_column2_menu_info();

			echo '<p class="description">';
			echo esc_html__('The footer second column displays a WordPress menu assigned to the "Footer Column 2 Menu" location.', 'mt-tickets');
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
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_column3_menu_hint',
		__('Footer Column 3 Menu', 'mt-tickets'),
		function () {
			$menus_url     = admin_url('nav-menus.php');
			$locations_url = admin_url('nav-menus.php?action=locations');

			$info = mt_tickets_get_footer_column3_menu_info();

			echo '<p class="description">';
			echo esc_html__('The footer third column displays a WordPress menu assigned to the "Footer Column 3 Menu" location.', 'mt-tickets');
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
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_column4_title',
		__('Footer Column 4 Title', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_footer_column4_title', 'For Contact');
			echo '<input type="text" name="mt_tickets_footer_column4_title" value="' . esc_attr($value) . '" class="regular-text" />';
			echo '<p class="description">' . esc_html__('The title displayed in the footer fourth column.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_column4_description',
		__('Footer Column 4 Description', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_footer_column4_description', "Address:\nPhone:\nEmail:\nOpening hours:");
			echo '<textarea name="mt_tickets_footer_column4_description" rows="6" class="large-text">' . esc_textarea($value) . '</textarea>';
			echo '<p class="description">' . esc_html__('The description displayed in the footer fourth column. Each line will be displayed as a separate paragraph.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_copyright_text',
		__('Footer Copyright Text', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_footer_copyright_text', '© MT Tickets. All rights reserved.');
			echo '<input type="text" name="mt_tickets_footer_copyright_text" value="' . esc_attr($value) . '" class="regular-text" />';
			echo '<p class="description">' . esc_html__('The copyright text displayed in the footer bottom row.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	add_settings_field(
		'mt_tickets_footer_payment_icons_id',
		__('Footer Payment Icons Image', 'mt-tickets'),
		function () {
			$image_id = (int) get_option('mt_tickets_footer_payment_icons_id', 0);
			$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
			$default_image_url = get_theme_file_uri('assets/images/icons_payment.png');

			echo '<div class="mt-footer-payment-icons-preview" style="margin-bottom:10px;">';
			if ($image_url) {
				echo '<img src="' . esc_url($image_url) . '" alt="Payment icons" style="max-height:24px;width:auto;display:block;margin-bottom:10px;" />';
			} else {
				echo '<img src="' . esc_url($default_image_url) . '" alt="Payment icons (default)" style="max-height:24px;width:auto;display:block;margin-bottom:10px;" />';
			}
			echo '</div>';

			echo '<input type="hidden" id="mt_tickets_footer_payment_icons_id" name="mt_tickets_footer_payment_icons_id" value="' . esc_attr($image_id) . '" />';
			echo '<button type="button" class="button" id="mt_footer_payment_icons_upload_btn">' . esc_html__('Select Image', 'mt-tickets') . '</button> ';
			if ($image_id) {
				echo '<button type="button" class="button" id="mt_footer_payment_icons_remove_btn">' . esc_html__('Remove', 'mt-tickets') . '</button> ';
			}
			echo '<p class="description">' . esc_html__('Select an image for payment method icons. Recommended height: 24px.', 'mt-tickets') . '</p>';

			// Media uploader script
			wp_enqueue_media();
?>
		<script>
			jQuery(document).ready(function($) {
				var uploadBtn = $('#mt_footer_payment_icons_upload_btn');
				var removeBtn = $('#mt_footer_payment_icons_remove_btn');
				var imageIdInput = $('#mt_tickets_footer_payment_icons_id');
				var preview = $('.mt-footer-payment-icons-preview');

				uploadBtn.on('click', function(e) {
					e.preventDefault();
					var mediaUploader = wp.media({
						title: '<?php echo esc_js(__('Select Payment Icons Image', 'mt-tickets')); ?>',
						button: {
							text: '<?php echo esc_js(__('Use this image', 'mt-tickets')); ?>'
						},
						multiple: false
					});

					mediaUploader.on('select', function() {
						var attachment = mediaUploader.state().get('selection').first().toJSON();
						imageIdInput.val(attachment.id);
						preview.html('<img src="' + attachment.url + '" alt="Payment icons" style="max-height:24px;width:auto;display:block;margin-bottom:10px;" />');
						if (!removeBtn.is(':visible')) {
							removeBtn.show();
						}
					});

					mediaUploader.open();
				});

				removeBtn.on('click', function(e) {
					e.preventDefault();
					imageIdInput.val('0');
					preview.html('<img src="<?php echo esc_js($default_image_url); ?>" alt="Payment icons (default)" style="max-height:24px;width:auto;display:block;margin-bottom:10px;" />');
					removeBtn.hide();
				});
			});
		</script>
	<?php
		},
		'mt-tickets-footer-settings',
		'mt_tickets_footer_section'
	);

	// Settings page - Back to top button
	register_setting('mt_tickets_settings_page', 'mt_tickets_back_to_top_enabled', array(
		'type'              => 'boolean',
		'sanitize_callback' => function ($value) {
			return (bool) $value;
		},
		'default'           => true,
	));

	add_settings_section(
		'mt_tickets_settings_section',
		__('General Settings', 'mt-tickets'),
		'__return_false',
		'mt-tickets-settings-page'
	);

	add_settings_field(
		'mt_tickets_back_to_top_enabled',
		__('Back to Top Button', 'mt-tickets'),
		function () {
			$value = get_option('mt_tickets_back_to_top_enabled', false);
			echo '<label>';
			echo '<input type="checkbox" name="mt_tickets_back_to_top_enabled" value="1" ' . checked($value, true, false) . ' />';
			echo ' ' . esc_html__('Show Back to Top button', 'mt-tickets');
			echo '</label>';
			echo '<p class="description">' . esc_html__('Enable a floating button in the bottom right corner that scrolls to the top of the page when clicked.', 'mt-tickets') . '</p>';
		},
		'mt-tickets-settings-page',
		'mt_tickets_settings_section'
	);
});

function mt_tickets_render_overview_page()
{
	if (! current_user_can('manage_options')) return;

	$theme = wp_get_theme();
	$theme_version = $theme->get('Version');
	$theme_name = $theme->get('Name');
	?>
	<div class="wrap">
		<h1><?php echo esc_html__('MT Tickets Theme', 'mt-tickets'); ?></h1>

		<div class="mt-tickets-overview" style="max-width: 1200px;">
			<div class="mt-tickets-overview__intro" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; margin: 20px 0;">
				<h2 style="margin-top: 0;"><?php echo esc_html__('Welcome to MT Tickets Theme', 'mt-tickets'); ?></h2>
				<p style="font-size: 15px; line-height: 1.6;">
					<?php
					printf(
						esc_html__('Thank you for using %s version %s. This theme is designed for ticket sales platforms, carriers, schedules, and reservations.', 'mt-tickets'),
						'<strong>' . esc_html($theme_name) . '</strong>',
						'<strong>' . esc_html($theme_version) . '</strong>'
					);
					?>
				</p>
			</div>

			<div class="mt-tickets-overview__sections" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
				<div class="mt-tickets-overview__section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__('Quick Links', 'mt-tickets'); ?></h3>
					<ul style="list-style: disc; padding-left: 20px;">
						<li><a href="<?php echo esc_url(admin_url('admin.php?page=mt-tickets-settings')); ?>"><?php echo esc_html__('Header Settings', 'mt-tickets'); ?></a></li>
						<li><a href="<?php echo esc_url(admin_url('admin.php?page=mt-tickets-footer-settings')); ?>"><?php echo esc_html__('Footer Settings', 'mt-tickets'); ?></a></li>
						<li><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php echo esc_html__('Manage Menus', 'mt-tickets'); ?></a></li>
						<li><a href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php echo esc_html__('Customize Theme', 'mt-tickets'); ?></a></li>
					</ul>
				</div>

				<div class="mt-tickets-overview__section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__('Documentation', 'mt-tickets'); ?></h3>
					<p><?php echo esc_html__('Comprehensive documentation will be available soon. Check back for detailed guides on:', 'mt-tickets'); ?></p>
					<ul style="list-style: disc; padding-left: 20px;">
						<li><?php echo esc_html__('Theme installation and setup', 'mt-tickets'); ?></li>
						<li><?php echo esc_html__('Customization options', 'mt-tickets'); ?></li>
						<li><?php echo esc_html__('Block usage and configuration', 'mt-tickets'); ?></li>
						<li><?php echo esc_html__('Troubleshooting guide', 'mt-tickets'); ?></li>
					</ul>
					<p style="margin-top: 15px;">
						<a href="#" class="button button-primary" target="_blank" rel="noopener"><?php echo esc_html__('View Documentation', 'mt-tickets'); ?></a>
					</p>
				</div>

				<div class="mt-tickets-overview__section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
					<h3 style="margin-top: 0;"><?php echo esc_html__('Support & Resources', 'mt-tickets'); ?></h3>
					<p><?php echo esc_html__('Need help? Get support and find additional resources:', 'mt-tickets'); ?></p>
					<ul style="list-style: disc; padding-left: 20px;">
						<li><a href="#" target="_blank" rel="noopener"><?php echo esc_html__('Support Forum', 'mt-tickets'); ?></a></li>
						<li><a href="#" target="_blank" rel="noopener"><?php echo esc_html__('FAQ', 'mt-tickets'); ?></a></li>
						<li><a href="#" target="_blank" rel="noopener"><?php echo esc_html__('Video Tutorials', 'mt-tickets'); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="mt-tickets-overview__promo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 30px; border-radius: 4px; margin: 20px 0;">
				<h2 style="color: #fff; margin-top: 0;"><?php echo esc_html__('Get More Features', 'mt-tickets'); ?></h2>
				<p style="font-size: 15px; line-height: 1.6; color: rgba(255, 255, 255, 0.9);">
					<?php echo esc_html__('Upgrade to premium version for advanced features, priority support, and regular updates.', 'mt-tickets'); ?>
				</p>
				<p style="margin-top: 15px;">
					<a href="#" class="button button-primary" target="_blank" rel="noopener" style="background: #fff; color: #667eea; border-color: #fff;"><?php echo esc_html__('Learn More', 'mt-tickets'); ?></a>
				</p>
			</div>

			<div class="mt-tickets-overview__info" style="background: #f0f0f1; padding: 15px; border-radius: 4px; margin: 20px 0;">
				<p style="margin: 0; font-size: 13px; color: #646970;">
					<strong><?php echo esc_html__('Theme Information:', 'mt-tickets'); ?></strong><br>
					<?php
					printf(
						esc_html__('Version: %s | License: %s | Author: %s', 'mt-tickets'),
						'<strong>' . esc_html($theme_version) . '</strong>',
						'<strong>GPL-2.0-or-later</strong>',
						'<strong>' . esc_html($theme->get('Author')) . '</strong>'
					);
					?>
				</p>
			</div>
		</div>
	</div>
<?php
}

function mt_tickets_render_header_settings_page()
{
	if (! current_user_can('manage_options')) return;
?>
	<div class="wrap">
		<h1><?php echo esc_html__('Header Settings', 'mt-tickets'); ?></h1>
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

function mt_tickets_render_footer_settings_page()
{
	if (! current_user_can('manage_options')) return;
?>
	<div class="wrap">
		<h1><?php echo esc_html__('Footer Settings', 'mt-tickets'); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('mt_tickets_footer_settings');
			do_settings_sections('mt-tickets-footer-settings');
			submit_button();
			?>
		</form>
	</div>
<?php
}

function mt_tickets_render_settings_page()
{
	if (! current_user_can('manage_options')) return;

	// Check if home page already exists
	$home_page_id = get_option('page_on_front', 0);
	$home_page_exists = $home_page_id > 0;
?>
	<div class="wrap">
		<h1><?php echo esc_html__('Settings', 'mt-tickets'); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('mt_tickets_settings_page');
			do_settings_sections('mt-tickets-settings-page');
			submit_button();
			?>
		</form>

		<div class="mt-create-home-page-section" style="margin-top: 30px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
			<h2><?php echo esc_html__('Home Page Setup', 'mt-tickets'); ?></h2>

			<?php if ($home_page_exists) : ?>
				<?php
				$home_page = get_post($home_page_id);
				if ($home_page) {
					$home_page_title = $home_page->post_title;
				} else {
					$home_page_title = __('Unknown', 'mt-tickets');
				}
				?>
				<p><?php
					printf(
						esc_html__('There is already a homepage created: %s', 'mt-tickets'),
						'<strong>' . esc_html($home_page_title) . '</strong>'
					);
					?></p>
				<p class="description" style="color: #d63638; margin-top: 10px;">
					<?php echo esc_html__('Creating a new home page will replace the current one as your front page.', 'mt-tickets'); ?>
				</p>
				<button type="button" id="mt-create-home-page-btn" class="button button-primary" style="margin-top: 10px;" data-has-home-page="true">
					<?php echo esc_html__('Create New Home Page', 'mt-tickets'); ?>
				</button>
			<?php else : ?>
				<p><?php echo esc_html__('No active home page.', 'mt-tickets'); ?></p>
				<p class="description" style="margin-top: 10px;">
					<?php echo esc_html__('Create a pre-configured home page with all the essential sections. This will set up your home page and activate it as the front page.', 'mt-tickets'); ?>
				</p>
				<button type="button" id="mt-create-home-page-btn" class="button button-primary" style="margin-top: 10px;" data-has-home-page="false">
					<?php echo esc_html__('Create Home Page', 'mt-tickets'); ?>
				</button>
			<?php endif; ?>

			<span id="mt-create-home-page-spinner" class="spinner" style="float: none; margin-left: 10px; visibility: hidden;"></span>
			<div id="mt-create-home-page-message" style="margin-top: 10px;"></div>
		</div>
	</div>

	<script>
		jQuery(document).ready(function($) {
			$('#mt-create-home-page-btn').on('click', function(e) {
				e.preventDefault();

				var $btn = $(this);
				var $spinner = $('#mt-create-home-page-spinner');
				var $message = $('#mt-create-home-page-message');
				var hasHomePage = $btn.data('has-home-page') === true;

				// Show confirmation dialog
				var confirmMessage = hasHomePage ?
					'<?php echo esc_js(__('Are you sure you want to create a new home page? This will replace your current home page as the front page.', 'mt-tickets')); ?>' :
					'<?php echo esc_js(__('Are you sure you want to create a new home page? This will set it as your front page.', 'mt-tickets')); ?>';

				if (!confirm(confirmMessage)) {
					return;
				}

				$btn.prop('disabled', true);
				$spinner.css('visibility', 'visible');
				$message.html('');

				$.ajax({
					url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
					type: 'POST',
					data: {
						action: 'mt_create_home_page',
						nonce: '<?php echo wp_create_nonce('mt_create_home_page_nonce'); ?>'
					},
					success: function(response) {
						$spinner.css('visibility', 'hidden');
						$btn.prop('disabled', false);

						if (response.success) {
							$message.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
							if (response.data.edit_url) {
								setTimeout(function() {
									window.location.href = response.data.edit_url;
								}, 2000);
							} else {
								setTimeout(function() {
									location.reload();
								}, 2000);
							}
						} else {
							$message.html('<div class="notice notice-error"><p>' + (response.data.message || '<?php echo esc_js(__('An error occurred. Please try again.', 'mt-tickets')); ?>') + '</p></div>');
						}
					},
					error: function() {
						$spinner.css('visibility', 'hidden');
						$btn.prop('disabled', false);
						$message.html('<div class="notice notice-error"><p><?php echo esc_js(__('An error occurred. Please try again.', 'mt-tickets')); ?></p></div>');
					}
				});
			});
		});
	</script>
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

function mt_tickets_get_footer_column2_menu_info()
{
	$assigned = has_nav_menu('mt_tickets_footer_column2');

	$menu_id = 0;
	$menu_name = '';

	if ($assigned) {
		$locations = get_nav_menu_locations();
		$menu_id = (int) ($locations['mt_tickets_footer_column2'] ?? 0);

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

function mt_tickets_get_footer_column3_menu_info()
{
	$assigned = has_nav_menu('mt_tickets_footer_column3');

	$menu_id = 0;
	$menu_name = '';

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

	foreach (array('topbar-left', 'topbar-menu', 'header-logo', 'header-menu', 'header-icons', 'header-hero', 'footer-column1', 'footer-column2', 'footer-column3', 'footer-column4', 'footer-copyright') as $b) {
		if (is_dir($base . '/' . $b)) {
			register_block_type($base . '/' . $b);
		}
	}
});

/**
 * Register block pattern categories for template parts.
 */
add_action('init', function () {
	if (function_exists('register_block_pattern_category')) {
		register_block_pattern_category('header', array(
			'label' => __('Header', 'mt-tickets'),
		));
		register_block_pattern_category('footer', array(
			'label' => __('Footer', 'mt-tickets'),
		));
		register_block_pattern_category('mt-tickets', array(
			'label' => __('MT Tickets', 'mt-tickets'),
		));
	}

	// Register block patterns
	if (function_exists('register_block_pattern')) {
		// Register Our Services pattern
		ob_start();
		include get_template_directory() . '/patterns/our-services.php';
		$our_services_content = ob_get_clean();
		register_block_pattern('mt-tickets/our-services', array(
			'title'       => __('Our Services', 'mt-tickets'),
			'description' => __('A services section with three feature cards.', 'mt-tickets'),
			'categories'  => array('mt-tickets'),
			'content'     => $our_services_content,
		));

		// Register About Us pattern
		ob_start();
		include get_template_directory() . '/patterns/about-us.php';
		$about_us_content = ob_get_clean();
		register_block_pattern('mt-tickets/about-us', array(
			'title'       => __('About Us', 'mt-tickets'),
			'description' => __('A two-column about section with image and feature highlights.', 'mt-tickets'),
			'categories'  => array('mt-tickets'),
			'content'     => $about_us_content,
		));

		// Register Our Fleet pattern
		ob_start();
		include get_template_directory() . '/patterns/our-fleet.php';
		$our_fleet_content = ob_get_clean();
		register_block_pattern('mt-tickets/our-fleet', array(
			'title'       => __('Our Fleet', 'mt-tickets'),
			'description' => __('A fleet section with product category display.', 'mt-tickets'),
			'categories'  => array('mt-tickets'),
			'content'     => $our_fleet_content,
		));

		// Register Testimonials pattern
		ob_start();
		include get_template_directory() . '/patterns/testimonials.php';
		$testimonials_content = ob_get_clean();
		register_block_pattern('mt-tickets/testimonials', array(
			'title'       => __('Testimonials', 'mt-tickets'),
			'description' => __('A testimonials section with three customer review cards.', 'mt-tickets'),
			'categories'  => array('mt-tickets'),
			'content'     => $testimonials_content,
		));
	}
});

/**
 * Filter template part categories in the editor.
 */
add_filter('wp_list_table_class_name', function ($class_name, $screen_id) {
	if ($screen_id === 'edit-wp_template_part') {
		// This filter helps organize template parts in the editor
	}
	return $class_name;
}, 10, 2);

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
			'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
			'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '',
		));
	}

	// Localize script for Account AJAX
	wp_localize_script('mt-tickets-ui', 'mtTicketsAccount', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce'    => wp_create_nonce('mt_tickets_account_nonce'),
	));
});

/**
 * AJAX handler for account login
 */
add_action('wp_ajax_mt_account_login', 'mt_account_login');
add_action('wp_ajax_nopriv_mt_account_login', 'mt_account_login');

function mt_account_login()
{
	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mt_tickets_account_nonce')) {
		wp_send_json_error(array('message' => __('Security check failed.', 'mt-tickets')));
		return;
	}

	// Get credentials
	$username = isset($_POST['log']) ? sanitize_user($_POST['log']) : '';
	$password = isset($_POST['pwd']) ? $_POST['pwd'] : '';
	$remember = isset($_POST['rememberme']) && $_POST['rememberme'] === 'forever';

	if (empty($username) || empty($password)) {
		wp_send_json_error(array('message' => __('Username and password are required.', 'mt-tickets')));
		return;
	}

	// Attempt login
	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => $remember,
	);

	$user = wp_signon($creds, false);

	if (is_wp_error($user)) {
		// Get error message
		$error_message = $user->get_error_message();

		// Default message if empty
		if (empty($error_message)) {
			$error_message = __('Unknown email address. Check again or try your username.', 'mt-tickets');
		}

		wp_send_json_error(array('message' => $error_message));
		return;
	}

	// Login successful
	wp_send_json_success(array(
		'message' => __('Login successful!', 'mt-tickets'),
		'redirect' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : admin_url('profile.php'),
	));
}

/**
 * AJAX handler for account registration (email only).
 */
add_action('wp_ajax_mt_account_register', 'mt_account_register');
add_action('wp_ajax_nopriv_mt_account_register', 'mt_account_register');

function mt_account_register()
{
	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mt_tickets_account_nonce')) {
		wp_send_json_error(array('message' => __('Security check failed.', 'mt-tickets')));
		return;
	}

	// Check if registration is allowed
	$registration_allowed = false;
	if (class_exists('WooCommerce')) {
		$registration_allowed = get_option('woocommerce_enable_myaccount_registration', 'no') === 'yes';
	}
	if (!$registration_allowed) {
		// Fallback to WP setting
		$registration_allowed = (bool) get_option('users_can_register', false);
	}
	if (!$registration_allowed) {
		wp_send_json_error(array('message' => __('Account registration is disabled.', 'mt-tickets')));
		return;
	}

	$email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';
	if (empty($email) || !is_email($email)) {
		wp_send_json_error(array('message' => __('Please enter a valid email address.', 'mt-tickets')));
		return;
	}

	if (email_exists($email)) {
		wp_send_json_error(array('message' => __('An account already exists for this email address. Please log in.', 'mt-tickets')));
		return;
	}

	// Derive username from email
	$username_base = sanitize_user(current(explode('@', $email)));
	if (empty($username_base)) {
		$username_base = 'user';
	}
	$username = $username_base;
	$suffix = 1;
	while (username_exists($username)) {
		$username = $username_base . $suffix;
		$suffix++;
	}

	// Generate password
	$password = wp_generate_password(12, true);

	$user_id = wp_create_user($username, $password, $email);

	if (is_wp_error($user_id)) {
		$error_message = $user_id->get_error_message();
		if (empty($error_message)) {
			$error_message = __('Registration failed. Please try again.', 'mt-tickets');
		}
		wp_send_json_error(array('message' => $error_message));
		return;
	}

	// Optionally send notification to user
	wp_new_user_notification($user_id, null, 'user');

	// Auto login the user
	wp_set_current_user($user_id);
	wp_set_auth_cookie($user_id, true);

	wp_send_json_success(array(
		'message' => __('Registration successful!', 'mt-tickets'),
		'redirect' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : admin_url('profile.php'),
	));
}

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
 * AJAX handler for refreshing mini cart content
 */
add_action('wp_ajax_mt_refresh_mini_cart', 'mt_refresh_mini_cart');
add_action('wp_ajax_nopriv_mt_refresh_mini_cart', 'mt_refresh_mini_cart');

function mt_refresh_mini_cart()
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

	$cart_count = (int) $cart->get_cart_contents_count();
	$cart_totals = $cart->get_totals();
	$cart_total_num = isset($cart_totals['total']) ? (float)$cart_totals['total'] : 0;
	$currency_symbol = get_woocommerce_currency_symbol();
	$currency_pos    = get_option('woocommerce_currency_pos', 'left');
	$decimals        = wc_get_price_decimals();
	$decimal_sep     = wc_get_price_decimal_separator();
	$thousand_sep    = wc_get_price_thousand_separator();
	$cart_items = $cart->get_cart();

	// Build cart items HTML
	ob_start();
	if ($cart_count > 0) {
		foreach ($cart_items as $cart_item_key => $cart_item) {
			$_product = $cart_item['data'];
			$product_id = $cart_item['product_id'];
			$quantity = $cart_item['quantity'];
			$product_permalink = $_product->get_permalink();
			$product_name = $_product->get_name();
			$product_price = $_product->get_price_html();
			$product_image = $_product->get_image(array(120, 120));
			$line_total = isset($cart_item['line_total']) ? (float)$cart_item['line_total'] : 0;
			$unit_price = $quantity > 0 ? $line_total / $quantity : 0;
	?>
			<div class="mt-mini-cart__item" data-line-total="<?php echo esc_attr($line_total); ?>" data-unit-price="<?php echo esc_attr($unit_price); ?>">
				<div class="mt-mini-cart__item-image">
					<?php echo $product_image; ?>
				</div>
				<div class="mt-mini-cart__item-details">
					<div class="mt-mini-cart__item-name">
						<a href="<?php echo esc_url($product_permalink); ?>">
							<?php echo esc_html($product_name); ?>
						</a>
					</div>
					<div class="mt-mini-cart__item-price"><?php echo $product_price; ?></div>
					<div class="mt-mini-cart__item-actions">
						<div class="mt-mini-cart__quantity">
							<button type="button" class="mt-mini-cart__qty-btn" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" data-action="decrease">−</button>
							<input type="number" class="mt-mini-cart__qty-input" value="<?php echo esc_attr($quantity); ?>" min="1" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" readonly>
							<button type="button" class="mt-mini-cart__qty-btn" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" data-action="increase">+</button>
						</div>
						<button type="button" class="mt-mini-cart__remove" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php echo esc_attr__('Remove item', 'mt-tickets'); ?>">
							<?php echo esc_html__('Remove', 'mt-tickets'); ?>
						</button>
					</div>
				</div>
			</div>
	<?php
		}
	} else {
		echo '<div class="mt-mini-cart__empty">' . esc_html__('Your cart is empty.', 'mt-tickets') . '</div>';
	}
	$cart_items_html = ob_get_clean();

	wp_send_json_success(array(
		'cart_count' => $cart_count,
		'cart_total' => wp_kses_post(wc_price($cart_total_num)),
		'cart_total_num' => $cart_total_num,
		'cart_items_html' => $cart_items_html,
		'currency_symbol' => $currency_symbol,
		'currency_position' => $currency_pos,
		'decimals' => $decimals,
		'decimal_sep' => $decimal_sep,
		'thousand_sep' => $thousand_sep,
	));
}

/**
 * Register a menu location for the top bar (right side).
 */
add_action('after_setup_theme', function () {
	register_nav_menus(array(
		'mt_tickets_topbar'  => __('Top Bar Menu', 'mt-tickets'),
		'mt_tickets_primary' => __('Primary Menu', 'mt-tickets'),
		'mt_tickets_footer_column2' => __('Footer Column 2 Menu', 'mt-tickets'),
		'mt_tickets_footer_column3' => __('Footer Column 3 Menu', 'mt-tickets'),
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

	register_rest_route('mt-tickets/v1', '/header-hero', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$default_title = 'Buy a bus ticket quickly and conveniently';
			$title = get_option('mt_tickets_header_hero_title', $default_title);

			$default_description = 'Search by destination, date and carrier';
			$description = get_option('mt_tickets_header_hero_description', $default_description);

			$shortcode = get_option('mt_tickets_header_hero_search_shortcode', '');

			return array(
				'title' => $title,
				'description' => $description,
				'shortcode' => $shortcode,
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/footerbar', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$logo_id = (int) get_option('mt_tickets_footer_logo_id', 0);
			$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
			$placeholder = get_theme_file_uri('assets/images/logo-placeholder.svg');

			$default_title = 'About the Platform';
			$title = get_option('mt_tickets_footer_column1_title', $default_title);

			$default_description = 'Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.';
			$description = get_option('mt_tickets_footer_description', $default_description);

			return array(
				'title' => $title,
				'logo' => array(
					'url' => $logo_url ?: $placeholder,
				),
				'description' => $description,
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/footer-column2', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$assigned = has_nav_menu('mt_tickets_footer_column2');
			$menu_name = '';
			$items_out = array();

			if ($assigned) {
				$locations = get_nav_menu_locations();
				$menu_id = (int) ($locations['mt_tickets_footer_column2'] ?? 0);
				if ($menu_id) {
					$term = get_term((int)$menu_id, 'nav_menu');
					if ($term && !is_wp_error($term)) {
						$menu_name = (string) $term->name;
					}
				}
			}

			return array(
				'menu' => array(
					'assigned' => (bool)$assigned,
					'name'     => $menu_name,
					'items'    => $items_out,
				),
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/footer-column3', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$assigned = has_nav_menu('mt_tickets_footer_column3');
			$menu_name = '';
			$items_out = array();

			if ($assigned) {
				$locations = get_nav_menu_locations();
				$menu_id = (int) ($locations['mt_tickets_footer_column3'] ?? 0);
				if ($menu_id) {
					$term = get_term((int)$menu_id, 'nav_menu');
					if ($term && !is_wp_error($term)) {
						$menu_name = (string) $term->name;
					}
				}
			}

			return array(
				'menu' => array(
					'assigned' => (bool)$assigned,
					'name'     => $menu_name,
					'items'    => $items_out,
				),
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/footer-column4', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$default_title = 'For Contact';
			$title = get_option('mt_tickets_footer_column4_title', $default_title);

			$default_description = "Address:\nPhone:\nEmail:\nOpening hours:";
			$description = get_option('mt_tickets_footer_column4_description', $default_description);

			return array(
				'title'       => $title,
				'description' => $description,
			);
		},
	));

	register_rest_route('mt-tickets/v1', '/footer-copyright', array(
		'methods'  => 'GET',
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		},
		'callback' => function () {
			$default_copyright = '© MT Tickets. All rights reserved.';
			$copyright = get_option('mt_tickets_footer_copyright_text', $default_copyright);

			$image_id = (int) get_option('mt_tickets_footer_payment_icons_id', 0);
			$image_url = '';
			$placeholder = get_theme_file_uri('assets/images/icons_payment.png');

			if ($image_id) {
				$image_url = wp_get_attachment_image_url($image_id, 'full');
			}

			return array(
				'copyright' => $copyright,
				'image'     => array(
					'id'  => $image_id,
					'url' => $image_url ?: $placeholder,
				),
			);
		},
	));
});

add_action('admin_enqueue_scripts', function ($hook) {
	// Load scripts for Overview, Header and Footer settings pages
	if (
		$hook !== 'toplevel_page_mt-tickets-overview' &&
		$hook !== 'mt-tickets_page_mt-tickets-overview' &&
		$hook !== 'mt-tickets_page_mt-tickets-settings' &&
		$hook !== 'mt-tickets_page_mt-tickets-settings-page' &&
		$hook !== 'mt-tickets_page_mt-tickets-footer-settings'
	) {
		return;
	}

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

/**
 * AJAX handler for creating home page
 */
add_action('wp_ajax_mt_create_home_page', 'mt_create_home_page');

function mt_create_home_page()
{
	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mt_create_home_page_nonce')) {
		wp_send_json_error(array('message' => __('Security check failed.', 'mt-tickets')));
		return;
	}

	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'mt-tickets')));
		return;
	}

	// Load home page content from file
	$home_content_file = get_template_directory() . '/includes/home-page-content.html';
	$home_content = '';

	if (file_exists($home_content_file)) {
		$home_content = file_get_contents($home_content_file);
		// Remove HTML comments that might interfere
		$home_content = preg_replace('/<!--\s*This file.*?-->/s', '', $home_content);
		$home_content = trim($home_content);
	}

	// Fallback to default content if file doesn't exist or is empty
	if (empty($home_content)) {
		$home_content = '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
	<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
	}

	// Always create a new page (don't check for existing)
	// This allows admin to create a fresh home page even if one already exists
	$page_data = array(
		'post_title'    => __('Home', 'mt-tickets'),
		'post_content'  => $home_content,
		'post_status'   => 'publish',
		'post_type'     => 'page',
		'post_author'   => get_current_user_id(),
	);

	$page_id = wp_insert_post($page_data);

	if (is_wp_error($page_id) || !$page_id) {
		wp_send_json_error(array('message' => __('Failed to create or update page.', 'mt-tickets')));
		return;
	}

	// Set as front page
	update_option('show_on_front', 'page');
	update_option('page_on_front', $page_id);

	// Get edit URL
	$edit_url = admin_url('post.php?post=' . $page_id . '&action=edit');

	wp_send_json_success(array(
		'message'  => __('Home page created successfully! Redirecting to edit page...', 'mt-tickets'),
		'page_id'  => $page_id,
		'edit_url' => $edit_url,
	));
}

/**
 * Add Back to Top button
 */
add_action('wp_footer', function () {
	$enabled = get_option('mt_tickets_back_to_top_enabled', true);
	if (!$enabled) {
		return;
	}
	?>
	<button id="mt-back-to-top" class="mt-back-to-top" aria-label="<?php echo esc_attr__('Back to top', 'mt-tickets'); ?>" title="<?php echo esc_attr__('Back to top', 'mt-tickets'); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M12 19V5M5 12l7-7 7 7" />
		</svg>
	</button>
<?php
});
