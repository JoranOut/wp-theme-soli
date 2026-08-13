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
	 * Temporarily store the releases fetched from GitHub.
	 *
	 * @var array|false|null
	 */
	private $releases_data;

	/**
	 * Temporarily store the resolved release for the installed version's channel.
	 *
	 * @var array|false|null
	 */
	private $channel_release;

	/**
	 * Whether the GitHub lookups have already run this request.
	 *
	 * @var bool
	 */
	private $remote_resolved = false;

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
		delete_site_transient( md5( $this->config['slug'] ) . '_releases' );

		// Drop the memoised state too, so the refetch actually happens. Do not
		// refetch here: this fires just before the update check, which calls
		// resolve_remote() itself.
		$this->github_data     = null;
		$this->releases_data   = null;
		$this->channel_release = null;
		$this->remote_resolved = false;
		unset( $this->config['new_version'] );
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

		// Local data only - nothing here may touch the network, see
		// resolve_remote(). Reading the installed version is what lets that
		// later lookup pick a channel, so it has to happen first.
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
	 * Resolve everything that needs a call to GitHub.
	 *
	 * Deliberately not called from the constructor. set_defaults() runs on
	 * `init` for every wp-admin request, so resolving there cost two API calls
	 * per admin page view - against GitHub's unauthenticated limit of 60 per
	 * hour, counted per source IP rather than per site or per theme. That is
	 * roughly 30 page views an hour for a single theme, and about 2.5 once a
	 * dozen plugins on the same host each do it, at which point every site on
	 * that IP starts reading stale data it cannot refresh.
	 *
	 * WordPress only needs any of this when it actually checks for updates -
	 * every 12 hours, or on an explicit force-check - so resolve on first use
	 * and memoise for the rest of the request.
	 *
	 * @since 1.0
	 */
	public function resolve_remote() {
		if ( $this->remote_resolved ) {
			return;
		}

		$this->remote_resolved = true;

		$release = $this->get_channel_release();

		if ( ! isset( $this->config['new_version'] ) ) {
			$this->config['new_version'] = ( false === $release ) ? false : $release['version'];
		}

		// Point the download at the release's built zip asset rather than at a
		// branch archive, so the update installs what CI actually packaged.
		if ( false !== $release && ! empty( $release['package'] ) ) {
			$this->config['zip_url'] = $release['package'];
		}

		if ( ! isset( $this->config['last_updated'] ) ) {
			$this->config['last_updated'] = $this->get_date();
		}

		if ( ! isset( $this->config['description'] ) ) {
			$this->config['description'] = $this->get_description();
		}
	}

	/**
	 * Whether a version string belongs to the nightly channel.
	 *
	 * Nightly builds are versioned `{stable}-nightly.{YYYYMMDD}` by
	 * .github/workflows/nightly.yml, which stamps that version into style.css
	 * in the zip it ships. A site is therefore on the nightly channel exactly
	 * when its installed version carries that suffix.
	 *
	 * @since 1.0
	 * @param string $version The version to classify.
	 * @return bool
	 */
	public function is_nightly_version( $version ) {
		return (bool) preg_match( '/-nightly\./i', (string) $version );
	}

	/**
	 * Get the repository's releases from the GitHub API.
	 *
	 * @since 1.0
	 * @return array|false The releases, or false when unavailable.
	 */
	public function get_releases() {
		if ( isset( $this->releases_data ) && ! empty( $this->releases_data ) ) {
			return $this->releases_data;
		}

		$transient_key = md5( $this->config['slug'] ) . '_releases';
		$cached        = get_site_transient( $transient_key );

		if ( ! $this->overrule_transients() && ! empty( $cached ) ) {
			$this->releases_data = $cached;
			return $cached;
		}

		$response = $this->remote_get( trailingslashit( $this->config['api_url'] ) . 'releases' );

		// An API failure - a rate limit above all, since the unauthenticated
		// limit is 60/hour and WP_GITHUB_FORCE_UPDATE re-checks on every admin
		// page load - must not be reported as "no update available". Fall back
		// to the last known good list instead.
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return empty( $cached ) ? false : $cached;
		}

		$releases = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $releases ) ) {
			return empty( $cached ) ? false : $cached;
		}

		// Cache for 6 hours.
		set_site_transient( $transient_key, $releases, 60 * 60 * 6 );

		$this->releases_data = $releases;

		return $releases;
	}

	/**
	 * Resolve the newest release within the installed version's channel.
	 *
	 * A stable install only ever sees stable releases and a nightly install
	 * only ever sees nightlies, so a site cannot cross channels by accident.
	 * Without this filter a nightly install would be offered the stable build
	 * as an "update", because version_compare() sorts `1.4.3-nightly.20260809`
	 * below `1.4.3`.
	 *
	 * @since 1.0
	 * @return array|false Release with keys 'version' and 'package', or false.
	 */
	public function get_channel_release() {
		if ( isset( $this->channel_release ) ) {
			return $this->channel_release;
		}

		$releases = $this->get_releases();

		if ( empty( $releases ) ) {
			return false;
		}

		$want_nightly = $this->is_nightly_version( $this->config['version'] );
		$best         = false;

		foreach ( $releases as $release ) {

			if ( ! empty( $release->draft ) ) {
				continue;
			}

			$version = ltrim( isset( $release->tag_name ) ? $release->tag_name : '', 'vV' );

			if ( '' === $version ) {
				continue;
			}

			if ( $this->is_nightly_version( $version ) !== $want_nightly ) {
				continue;
			}

			// Only a release carrying a built zip asset is installable; the
			// GitHub source zipball is an unbuilt tree.
			$package = '';

			if ( ! empty( $release->assets ) ) {
				foreach ( $release->assets as $asset ) {
					if ( ! empty( $asset->name ) && '.zip' === strtolower( substr( $asset->name, -4 ) ) ) {
						$package = $asset->browser_download_url;
						break;
					}
				}
			}

			if ( '' === $package ) {
				continue;
			}

			// GitHub returns releases newest-created first, but order by
			// version so a re-published older tag cannot win.
			if ( false === $best || 1 === version_compare( $version, $best['version'] ) ) {
				$best = array(
					'version' => $version,
					'package' => $package,
				);
			}
		}

		$this->channel_release = $best;

		return $best;
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
		// The download happens in a later request than the update check, so
		// config['zip_url'] may still hold the unresolved fallback by then -
		// match any URL on the repo instead. Never resolve from inside this
		// filter: it runs on every HTTP request, including our own, and would
		// recurse.
		if ( $this->config['zip_url'] === $url
			|| ( ! empty( $this->config['github_url'] ) && 0 === strpos( $url, $this->config['github_url'] ) ) ) {
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
		$release = $this->get_channel_release();

		return ( false === $release ) ? false : $release['version'];
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

		// Resolve here rather than in the constructor: this runs only when
		// WordPress genuinely checks for updates, not on every admin request.
		$this->resolve_remote();

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
