<?php
/**
 * AJAX Registration Helper Functions
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare( strict_types=1 );

use ArrayPress\WP\Register\Ajax;

if ( ! function_exists( 'register_ajax_actions' ) ):
	/**
	 * Helper function to register WordPress AJAX actions.
	 *
	 * Example usage:
	 * ```php
	 * $actions = [
	 *     'get_items' => [
	 *         'callback' => 'get_items_callback',
	 *         'public' => true,
	 *         'methods' => ['GET']
	 *     ],
	 *     'save_item' => [
	 *         'callback' => 'save_item_callback',
	 *         'capability' => 'edit_posts',
	 *         'args' => [
	 *             'title' => [
	 *                 'required' => true,
	 *                 'type' => 'string'
	 *             ],
	 *             'content' => [
	 *                 'type' => 'string'
	 *             ]
	 *         ]
	 *     ]
	 * ];
	 *
	 * register_ajax_actions($actions, 'my-plugin');
	 * ```
	 *
	 * @param array  $actions Array of AJAX actions
	 * @param string $prefix  Optional prefix for actions
	 *
	 * @return bool True on success, false on failure
	 */
	function register_ajax_actions( array $actions, string $prefix = '' ): bool {
		try {
			$ajax = new Ajax( $prefix );

			return ! empty( $actions ) && $ajax->add_actions( $actions ) instanceof Ajax;
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf( 'AJAX registration failed: %s', $e->getMessage() ) );
			}

			return false;
		}
	}
endif;

if ( ! function_exists( 'get_ajax_nonce' ) ):
	/**
	 * Helper function to get nonce for an AJAX action.
	 *
	 * Example usage:
	 * ```php
	 * $nonce = get_ajax_nonce('save_item', 'my-plugin');
	 * ```
	 *
	 * @param string $action Action name
	 * @param string $prefix Optional prefix used during registration
	 *
	 * @return string|null Nonce if action exists, null otherwise
	 */
	function get_ajax_nonce( string $action, string $prefix = '' ): ?string {
		$ajax = new Ajax( $prefix );

		return $ajax->get_nonce( $action );
	}
endif;

if ( ! function_exists( 'get_ajax_url' ) ):
	/**
	 * Helper function to get URL for an AJAX action.
	 *
	 * Example usage:
	 * ```php
	 * $url = get_ajax_url('get_items', 'my-plugin');
	 * ```
	 *
	 * @param string $action Action name
	 * @param string $prefix Optional prefix used during registration
	 *
	 * @return string|null URL if action exists, null otherwise
	 */
	function get_ajax_url( string $action, string $prefix = '' ): ?string {
		$ajax = new Ajax( $prefix );

		return $ajax->get_action_url( $action );
	}
endif;

if ( ! function_exists( 'format_ajax_response' ) ):
	/**
	 * Helper function to format AJAX response data.
	 *
	 * Example usage:
	 * ```php
	 * wp_send_json(format_ajax_response([
	 *     'items' => $items,
	 *     'total' => $total
	 * ]));
	 * ```
	 *
	 * @param mixed  $data    Response data
	 * @param bool   $success Whether the request was successful
	 * @param string $message Optional message
	 *
	 * @return array Formatted response
	 */
	function format_ajax_response( $data = null, bool $success = true, string $message = '' ): array {
		return [
			'success' => $success,
			'data'    => $data,
			'message' => $message
		];
	}
endif;