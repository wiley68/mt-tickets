<?php

/**
 * WordPress Core Functions Stubs for Intelephense
 * These are minimal stubs to prevent "unknown function" errors
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

// Constants
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}
