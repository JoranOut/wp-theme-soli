<?php
/**
 * GitHub Theme Updater.
 *
 * @package Soli_Theme_Soli
 * @since 1.0.0
 */

namespace Soli\ThemeSoli;

// Prevent loading this file directly and/or if the class is already defined.
if ( ! defined( 'ABSPATH' ) || class_exists( __NAMESPACE__ . '\WP_GitHub_Theme_Updater' ) ) {
	return;
}

/**
 * GitHub Theme Updater class.
 *
 * @version 1.0
 * @author Joachim Kudish <info@jkudish.com> (original plugin version)
 * @author Muziekvereniging Soli (theme adaptation)
 * @link http://jkudish.com
 * @package WP_GitHub_Theme_Updater
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright Copyright (c) 2011-2013, Joachim Kudish
 *
 * GNU General Public License, Free Software Foundation
 * <http://creativecommons.org/licenses/GPL/2.0/>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
class WP_GitHub_Theme_Updater {

	/**
	 * GitHub Theme Updater version.
	 */
	const VERSION = '1.0';

	/**
	 * The config for the updater.
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Any config that is missing from the initialization.
	 *
	 * @var array
	 */
	public $missing_config;

	/**
	 * Temporarily store the data fetched from GitHub.
	 *
	 * @var object|null
	 */
	private $github_data;

	/**
	 * Class Constructor.
	 *
	 * @since 1.0
	 * @param array $config The configuration required for the updater to work.
	 */
	public function __construct( $config = array() ) {
		$defaults = array(
			'slug'         => '',
			'sslverify'    => true,
			'access_token' => '',
		);

		$this->config = wp_parse_args( $config, $defaults );

		// If the minimum config isn't set, issue a warning and bail.
		if ( ! $this->has_minimum_config() ) {
			$message  = 'The GitHub Theme Updater was initialized without the minimum required configuration. ';
			$message .= 'The following params are missing: ' . implode( ', ', $this->missing_config );
			_doing_it_wrong( __CLASS__, esc_html( $message ), self::VERSION );
			return;
		}

		$this->set_defaults();

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'api_check' ) );
		add_action( 'delete_site_transient_update_themes', array( $this, 'delete_transients' ) );
		add_filter( 'http_request_timeout', array( $this, 'http_request_timeout' ) );
		add_filter( 'http_request_args', array( $this, 'http_request_sslverify' ), 10, 2 );
	}

	/**
	 * Check if minimum config is set.
	 *
	 * @since 1.0
	 * @return bool
	 */
	public function has_minimum_config() {
		$this->missing_config = array();

		$required_config_params = array(
			'api_url',
			'raw_url',
			'github_url',
			'zip_url',
			'requires',
			'tested',
			'readme',
			'slug',
		);

		foreach ( $required_config_params as $required_param ) {
			if ( empty( $this->config[ $required_param ] ) ) {
				$this->missing_config[] = $required_param;
			}
		}

		return empty( $this->missing_config );
	}

	/**
	 * Check whether or not the transients need to be overruled.
	 *
	 * @since 1.0
	 * @return bool
	 */
	public function overrule_transients() {
		return ( defined( 'WP_GITHUB_FORCE_UPDATE' ) && WP_GITHUB_FORCE_UPDATE );
	}

	/**
	 * Clear cached GitHub data when WordPress clears its update transient.
	 *
	 * Fires when the user clicks "Check again" on the Updates page.
	 *
	 * @since 1.0
	 */
	public function delete_transients() {
		delete_site_transient( md5( $this->config['slug'] ) . '_new_version' );
		delete_site_transient( md5( $this->config['slug'] ) . '_github_data' );
	}

	/**
	 * Set defaults.
	 *
	 * @since 1.0
	 */
	public function set_defaults() {
		if ( ! empty( $this->config['access_token'] ) ) {
			$parsed   = wp_parse_url( $this->config['zip_url'] );
			$zip_url  = $parsed['scheme'] . '://api.github.com/repos' . $parsed['path'];
			$zip_url  = add_query_arg( array( 'access_token' => $this->config['access_token'] ), $zip_url );

			$this->config['zip_url'] = $zip_url;
		}

		if ( ! isset( $this->config['new_version'] ) ) {
			$this->config['new_version'] = $this->get_new_version();
		}

		if ( ! isset( $this->config['last_updated'] ) ) {
			$this->config['last_updated'] = $this->get_date();
		}

		if ( ! isset( $this->config['description'] ) ) {
			$this->config['description'] = $this->get_description();
		}

		$theme = wp_get_theme( $this->config['slug'] );
		if ( ! isset( $this->config['theme_name'] ) ) {
			$this->config['theme_name'] = $theme->get( 'Name' );
		}

		if ( ! isset( $this->config['version'] ) ) {
			$this->config['version'] = $theme->get( 'Version' );
		}

		if ( ! isset( $this->config['author'] ) ) {
			$this->config['author'] = $theme->get( 'Author' );
		}

		if ( ! isset( $this->config['homepage'] ) ) {
			$this->config['homepage'] = $theme->get( 'ThemeURI' );
		}

		if ( ! isset( $this->config['readme'] ) ) {
			$this->config['readme'] = 'README.md';
		}
	}

	/**
	 * Callback for http_request_timeout filter.
	 *
	 * @since 1.0
	 * @return int
	 */
	public function http_request_timeout() {
		return 2;
	}

	/**
	 * Callback for http_request_args filter.
	 *
	 * @since 1.0
	 * @param array  $args Request arguments.
	 * @param string $url  Request URL.
	 * @return array
	 */
	public function http_request_sslverify( $args, $url ) {
		if ( $this->config['zip_url'] === $url ) {
			$args['sslverify'] = $this->config['sslverify'];
		}
		return $args;
	}

	/**
	 * Get new version from GitHub.
	 *
	 * @since 1.0
	 * @return string|false
	 */
	public function get_new_version() {
		$version = get_site_transient( md5( $this->config['slug'] ) . '_new_version' );

		if ( $this->overrule_transients() || empty( $version ) ) {
			// Try to get version from style.css.
			$raw_response = $this->remote_get( trailingslashit( $this->config['raw_url'] ) . 'style.css' );

			if ( is_wp_error( $raw_response ) ) {
				$version = false;
			} elseif ( is_array( $raw_response ) && ! empty( $raw_response['body'] ) ) {
				preg_match( '/.*Version\:\s*(.*)$/mi', $raw_response['body'], $matches );
				$version = ! empty( $matches[1] ) ? trim( $matches[1] ) : false;
			}

			// Fallback to README.md version.
			if ( false === $version ) {
				$raw_response = $this->remote_get( trailingslashit( $this->config['raw_url'] ) . $this->config['readme'] );

				if ( ! is_wp_error( $raw_response ) && is_array( $raw_response ) && ! empty( $raw_response['body'] ) ) {
					preg_match( '#^\s*`*~Current Version\:\s*([^~]*)~#im', $raw_response['body'], $version_match );
					if ( isset( $version_match[1] ) ) {
						$version = trim( $version_match[1] );
					}
				}
			}

			// Cache for 6 hours.
			if ( false !== $version ) {
				set_site_transient( md5( $this->config['slug'] ) . '_new_version', $version, 60 * 60 * 6 );
			}
		}

		return $version;
	}

	/**
	 * Interact with GitHub.
	 *
	 * @since 1.0
	 * @param string $query The query URL.
	 * @return array|WP_Error
	 */
	public function remote_get( $query ) {
		if ( ! empty( $this->config['access_token'] ) ) {
			$query = add_query_arg( array( 'access_token' => $this->config['access_token'] ), $query );
		}

		return wp_remote_get(
			$query,
			array(
				'sslverify' => $this->config['sslverify'],
			)
		);
	}

	/**
	 * Get GitHub data from the specified repository.
	 *
	 * @since 1.0
	 * @return object|false
	 */
	public function get_github_data() {
		if ( isset( $this->github_data ) && ! empty( $this->github_data ) ) {
			return $this->github_data;
		}

		$github_data = get_site_transient( md5( $this->config['slug'] ) . '_github_data' );

		if ( $this->overrule_transients() || empty( $github_data ) ) {
			$github_data = $this->remote_get( $this->config['api_url'] );

			if ( is_wp_error( $github_data ) ) {
				return false;
			}

			$github_data = json_decode( $github_data['body'] );

			// Cache for 6 hours.
			set_site_transient( md5( $this->config['slug'] ) . '_github_data', $github_data, 60 * 60 * 6 );
		}

		$this->github_data = $github_data;

		return $github_data;
	}

	/**
	 * Get update date.
	 *
	 * @since 1.0
	 * @return string|false
	 */
	public function get_date() {
		$date = $this->get_github_data();
		return ( ! empty( $date->updated_at ) ) ? gmdate( 'Y-m-d', strtotime( $date->updated_at ) ) : false;
	}

	/**
	 * Get theme description.
	 *
	 * @since 1.0
	 * @return string|false
	 */
	public function get_description() {
		$description = $this->get_github_data();
		return ( ! empty( $description->description ) ) ? $description->description : false;
	}

	/**
	 * Hook into the theme update check and connect to GitHub.
	 *
	 * @since 1.0
	 * @param object $transient The theme data transient.
	 * @return object
	 */
	public function api_check( $transient ) {
		// Check if the transient contains the 'checked' information.
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Check the version and decide if it's new.
		$update = version_compare( $this->config['new_version'], $this->config['version'] );

		if ( 1 === $update ) {
			$response = array(
				'theme'        => $this->config['slug'],
				'new_version'  => $this->config['new_version'],
				'url'          => add_query_arg( array( 'access_token' => $this->config['access_token'] ), $this->config['github_url'] ),
				'package'      => $this->config['zip_url'],
				'requires'     => $this->config['requires'],
				'requires_php' => $this->config['requires_php'] ?? '8.0',
			);

			$transient->response[ $this->config['slug'] ] = $response;
		}

		return $transient;
	}
}
