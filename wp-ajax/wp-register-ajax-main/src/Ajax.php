<?php
/**
 * AJAX Registration Manager
 *
 * A comprehensive solution for managing WordPress AJAX actions with features like:
 * - Public and private AJAX endpoints
 * - Nonce validation
 * - Capability checks
 * - Response formatting
 * - Error handling
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare(strict_types=1);

namespace ArrayPress\WP\Register;

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Class Ajax
 *
 * Manages WordPress AJAX action registration and handling.
 *
 * @since 1.0.0
 */
class Ajax {

	/**
	 * Collection of AJAX actions to be registered
	 *
	 * @var array
	 */
	private array $actions = [];

	/**
	 * Debug mode status
	 *
	 * @var bool
	 */
	private bool $debug = false;

	/**
	 * Prefix for actions and debug logs
	 *
	 * @var string
	 */
	private string $prefix = '';

	/**
	 * Constructor
	 *
	 * @param string $prefix Optional prefix for actions
	 */
	public function __construct(string $prefix = '') {
		$this->debug = defined('WP_DEBUG') && WP_DEBUG;

		if (!empty($prefix)) {
			$this->set_prefix($prefix);
		}
	}

	/**
	 * Set the prefix
	 *
	 * @param string $prefix The prefix to use
	 *
	 * @return self
	 */
	public function set_prefix(string $prefix): self {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * Add multiple AJAX actions
	 *
	 * @param array $actions Array of actions
	 *
	 * @return self
	 */
	public function add_actions(array $actions): self {
		foreach ($actions as $action => $config) {
			$this->add_action($action, $config);
		}
		return $this;
	}

	/**
	 * Add a single AJAX action
	 *
	 * @param string $action Action name
	 * @param array  $config Action configuration
	 *
	 * @return self
	 */
	public function add_action(string $action, array $config): self {
		if (!$this->is_valid_action_name($action)) {
			$this->log(sprintf('Invalid action name: %s', $action));
			return $this;
		}

		if (!isset($config['callback']) || !is_callable($config['callback'])) {
			$this->log(sprintf('Invalid callback for action: %s', $action));
			return $this;
		}

		$config = wp_parse_args($config, [
			'callback'    => null,
			'public'      => false,          // If true, available to non-logged-in users
			'capability'  => '',             // Required capability
			'verify_nonce' => true,          // Whether to verify nonce
			'nonce_key'   => '',             // Custom nonce key (defaults to action name)
			'methods'     => ['POST'],       // Allowed HTTP methods
			'args'        => []              // Expected arguments and their validation rules
		]);

		// Add prefix to action if set
		$prefixed_action = $this->maybe_prefix_action($action);

		// Store action configuration
		$this->actions[$prefixed_action] = $config;

		// Register WordPress hooks
		$this->register_action_hooks($prefixed_action, $config);

		return $this;
	}

	/**
	 * Register WordPress hooks for an action
	 *
	 * @param string $action Action name
	 * @param array  $config Action configuration
	 */
	protected function register_action_hooks(string $action, array $config): void {
		// Register for logged-in users
		add_action("wp_ajax_{$action}", function() use ($action, $config) {
			$this->handle_ajax_request($action, $config);
		});

		// Register for non-logged-in users if public
		if ($config['public']) {
			add_action("wp_ajax_nopriv_{$action}", function() use ($action, $config) {
				$this->handle_ajax_request($action, $config);
			});
		}
	}

	/**
	 * Handle AJAX request
	 *
	 * @param string $action Action name
	 * @param array  $config Action configuration
	 */
	protected function handle_ajax_request(string $action, array $config): void {
		try {
			// Verify HTTP method
			if (!in_array($_SERVER['REQUEST_METHOD'], $config['methods'])) {
				throw new \Exception('Invalid request method');
			}

			// Check capabilities
			if (!empty($config['capability']) && !current_user_can($config['capability'])) {
				throw new \Exception('Insufficient permissions');
			}

			// Verify nonce if required
			if ($config['verify_nonce']) {
				$nonce_key = $config['nonce_key'] ?: $action;
				$nonce = $_REQUEST['_ajax_nonce'] ?? $_REQUEST['nonce'] ?? '';
				if (!wp_verify_nonce($nonce, $nonce_key)) {
					throw new \Exception('Invalid nonce');
				}
			}

			// Validate arguments
			$args = $this->validate_args($_REQUEST, $config['args']);

			// Call the callback
			$response = call_user_func($config['callback'], $args);

			// Send response
			wp_send_json_success($response);

		} catch (\Exception $e) {
			$this->log(sprintf('AJAX error in %s: %s', $action, $e->getMessage()));
			wp_send_json_error([
				'message' => $e->getMessage()
			]);
		}
	}

	/**
	 * Validate request arguments
	 *
	 * @param array $request Request data
	 * @param array $args    Expected arguments
	 *
	 * @return array Validated arguments
	 * @throws \Exception If validation fails
	 */
	protected function validate_args(array $request, array $args): array {
		$validated = [];

		foreach ($args as $key => $rules) {
			// Check required args
			if (!empty($rules['required']) && !isset($request[$key])) {
				throw new \Exception(sprintf('Missing required parameter: %s', $key));
			}

			// Skip if not provided and not required
			if (!isset($request[$key])) {
				continue;
			}

			$value = $request[$key];

			// Type validation
			if (!empty($rules['type'])) {
				$valid = $this->validate_type($value, $rules['type']);
				if (!$valid) {
					throw new \Exception(sprintf('Invalid type for %s', $key));
				}
			}

			// Custom validation callback
			if (!empty($rules['validate']) && is_callable($rules['validate'])) {
				$valid = call_user_func($rules['validate'], $value);
				if (!$valid) {
					throw new \Exception(sprintf('Validation failed for %s', $key));
				}
			}

			$validated[$key] = $value;
		}

		return $validated;
	}

	/**
	 * Validate value type
	 *
	 * @param mixed  $value Value to check
	 * @param string $type  Expected type
	 *
	 * @return bool Whether the type is valid
	 */
	protected function validate_type($value, string $type): bool {
		switch ($type) {
			case 'int':
			case 'integer':
				return filter_var($value, FILTER_VALIDATE_INT) !== false;
			case 'float':
			case 'number':
				return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
			case 'string':
				return is_string($value);
			case 'array':
				return is_array($value);
			case 'bool':
			case 'boolean':
				return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== null;
			default:
				return true;
		}
	}

	/**
	 * Get the nonce for an action
	 *
	 * @param string $action Action name
	 *
	 * @return string|null Nonce if action exists, null otherwise
	 */
	public function get_nonce(string $action): ?string {
		$prefixed_action = $this->maybe_prefix_action($action);

		if (!isset($this->actions[$prefixed_action])) {
			return null;
		}

		$config = $this->actions[$prefixed_action];
		$nonce_key = $config['nonce_key'] ?: $prefixed_action;

		return wp_create_nonce($nonce_key);
	}

	/**
	 * Get the URL for an action
	 *
	 * @param string $action Action name
	 *
	 * @return string|null URL if action exists, null otherwise
	 */
	public function get_action_url(string $action): ?string {
		$prefixed_action = $this->maybe_prefix_action($action);

		if (!isset($this->actions[$prefixed_action])) {
			return null;
		}

		return admin_url('admin-ajax.php');
	}

	/**
	 * Validate action name
	 *
	 * @param string $action Action name to validate
	 *
	 * @return bool Whether the action name is valid
	 */
	protected function is_valid_action_name(string $action): bool {
		return (bool)preg_match('/^[a-zA-Z0-9_-]+$/', $action);
	}

	/**
	 * Maybe prefix action name
	 *
	 * @param string $action Action name
	 *
	 * @return string Possibly prefixed action name
	 */
	protected function maybe_prefix_action(string $action): string {
		return empty($this->prefix) ? $action : "{$this->prefix}_{$action}";
	}

	/**
	 * Log debug message
	 *
	 * @param string $message Message to log
	 * @param array  $context Optional context
	 */
	protected function log(string $message, array $context = []): void {
		if ($this->debug) {
			$prefix = $this->prefix ? "[{$this->prefix}] " : '';
			error_log(sprintf(
				'%sAJAX: %s %s',
				$prefix,
				$message,
				$context ? json_encode($context) : ''
			));
		}
	}
}