<?php

/**
 * WordPress Core Functions Stubs for Intelephense
 * These are minimal stubs to prevent "unknown function" errors
 *
 * This file is ONLY for IDE support and should NEVER be loaded in production.
 * Intelephense will automatically detect and use this file for type hints.
 */

// Theme functions
if (!function_exists('load_theme_textdomain')) {
	/**
	 * Load theme textdomain for translations
	 * @param string $domain
	 * @param string $path
	 * @return bool
	 */
	function load_theme_textdomain(string $domain, string $path = ''): bool
	{
		return true;
	}
}

if (!function_exists('get_template_directory')) {
	/**
	 * Get template directory path
	 * @return string
	 */
	function get_template_directory(): string
	{
		return '';
	}
}

// Hook system
if (!function_exists('add_action')) {
	/**
	 * Hook a function on to a specific action
	 * @param string $tag
	 * @param callable $function_to_add
	 * @param int $priority
	 * @param int $accepted_args
	 */
	function add_action(string $tag, callable $function_to_add, int $priority = 10, int $accepted_args = 1): void {}
}

// Admin menu functions
if (!function_exists('add_theme_page')) {
	/**
	 * Add a top level menu page in the 'Appearance' section
	 * @param string $page_title
	 * @param string $menu_title
	 * @param string $capability
	 * @param string $menu_slug
	 * @param callable $function
	 * @param int $position
	 * @return string
	 */
	function add_theme_page(string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = null, int $position = null): string
	{
		return '';
	}
}

// Translation functions
if (!function_exists('__')) {
	/**
	 * Translate string
	 * @param string $text
	 * @param string $domain
	 * @return string
	 */
	function __(string $text, string $domain = 'default'): string
	{
		return $text;
	}
}

if (!function_exists('_e')) {
	/**
	 * Echo translated string
	 * @param string $text
	 * @param string $domain
	 */
	function _e(string $text, string $domain = 'default'): void
	{
		echo $text;
	}
}

// Settings API functions
if (!function_exists('register_setting')) {
	/**
	 * Register a setting and its data
	 * @param string $option_group
	 * @param string $option_name
	 * @param array $args
	 */
	function register_setting(string $option_group, string $option_name, array $args = []): void {}
}

if (!function_exists('add_settings_section')) {
	/**
	 * Add a new section to a settings page
	 * @param string $id
	 * @param string $title
	 * @param callable $callback
	 * @param string $page
	 */
	function add_settings_section(string $id, string $title, callable $callback, string $page): void {}
}

if (!function_exists('add_settings_field')) {
	/**
	 * Add a new field to a section of a settings page
	 * @param string $id
	 * @param string $title
	 * @param callable $callback
	 * @param string $page
	 * @param string $section
	 * @param array $args
	 */
	function add_settings_field(string $id, string $title, callable $callback, string $page, string $section = '', array $args = []): void {}
}

if (!function_exists('settings_fields')) {
	/**
	 * Output nonce, action, and option_page fields for a settings page
	 * @param string $option_group
	 */
	function settings_fields(string $option_group): void {}
}

if (!function_exists('do_settings_sections')) {
	/**
	 * Prints out all settings sections added to a particular settings page
	 * @param string $page
	 */
	function do_settings_sections(string $page): void {}
}

// Data functions
if (!function_exists('get_option')) {
	/**
	 * Retrieve option value based on name of option
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
	function get_option(string $option, $default = false)
	{
		return $default;
	}
}

// Sanitization functions
if (!function_exists('sanitize_text_field')) {
	/**
	 * Sanitize a string from user input or from the database
	 * @param string $str
	 * @return string
	 */
	function sanitize_text_field(string $str): string
	{
		return $str;
	}
}

// Security functions
if (!function_exists('current_user_can')) {
	/**
	 * Whether current user has a specific capability
	 * @param string $capability
	 * @return bool
	 */
	function current_user_can(string $capability): bool
	{
		return true;
	}
}

// Escaping functions
if (!function_exists('esc_attr')) {
	/**
	 * Escaping for HTML attributes
	 * @param string $text
	 * @return string
	 */
	function esc_attr(string $text): string
	{
		return $text;
	}
}

if (!function_exists('esc_html__')) {
	/**
	 * Translate and escape string for output in HTML
	 * @param string $text
	 * @param string $domain
	 * @return string
	 */
	function esc_html__(string $text, string $domain = 'default'): string
	{
		return $text;
	}
}

// Form functions
if (!function_exists('submit_button')) {
	/**
	 * Echo a submit button, with provided text and appropriate class
	 * @param string $text
	 * @param string $type
	 * @param string $name
	 * @param bool $wrap
	 * @param array $other_attributes
	 */
	function submit_button(string $text = '', string $type = 'primary', string $name = 'submit', bool $wrap = true, array $other_attributes = []): void {}
}

// Block functions
if (!function_exists('register_block_bindings_source')) {
	/**
	 * Register a new block bindings source
	 * @param string $source_name
	 * @param array $source_properties
	 */
	function register_block_bindings_source(string $source_name, array $source_properties): void {}
}

if (!function_exists('get_block_wrapper_attributes')) {
	/**
	 * Get block wrapper attributes
	 * @param array $extra_attributes Optional. Array of extra attributes to render on the block wrapper.
	 * @return string String of HTML attributes.
	 */
	function get_block_wrapper_attributes($extra_attributes = array()): string
	{
		return '';
	}
}


if (!function_exists('register_block_type')) {
	/**
	 * Register a block type
	 * @param string|array $block_type
	 * @param array $args
	 * @return WP_Block_Type|bool
	 */
	function register_block_type($block_type, array $args = [])
	{
		return false;
	}
}

// Constants
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}
