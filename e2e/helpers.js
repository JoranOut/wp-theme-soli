/**
 * Shared helpers for the Soli theme e2e tests.
 */

const { expect } = require( '@playwright/test' );

const ADMIN_USER = 'admin';
const ADMIN_PASSWORD = 'password';

/**
 * Path fragment that identifies this theme's own PHP files.
 *
 * wp-env mounts the repository root at `wp-content/themes/wp-theme-soli`, and
 * PHP prints absolute paths in its diagnostics. Every PHP file this repository
 * owns therefore lives under this fragment: the template files at the theme
 * root (`functions.php`, `front-page.php`, `single.php`, `archive.php`,
 * `404.php`, `header.php`, `footer.php`, …) as well as everything under
 * `template-parts/`, `tribe-events/` and `theme-config/`.
 *
 * Scoping the softer diagnostics this way keeps unrelated WordPress core noise
 * from turning CI red without hiding anything the theme itself emits.
 */
const THEME_PHP_FILES = 'themes/wp-theme-soli/';

/** Diagnostics that are never acceptable, wherever they come from. */
const FATAL_ERROR_PATTERN = /Fatal error|Parse error/i;

/** Softer diagnostics, but only when they point at this theme's files. */
const THEME_DIAGNOSTIC_PATTERN = new RegExp(
	'(Warning|Notice|Deprecated):[^\\n]*(' + THEME_PHP_FILES + ')',
	'i'
);

/**
 * Asserts that the currently loaded page contains no PHP diagnostics.
 *
 * `WP_DEBUG` and `WP_DEBUG_DISPLAY` are enabled for the wp-env `tests`
 * environment (see `.wp-env.json`), so PHP diagnostics are printed into the
 * rendered document. Anything PHP emits before `<html>` or inside `<head>` is
 * relocated into the body by the HTML parser, so reading the body text catches
 * diagnostics from any point in the request.
 *
 * @param {import('@playwright/test').Page} page
 */
async function expectNoPhpDiagnostics( page ) {
	const url = page.url();
	const body = await page.locator( 'body' ).innerText();

	expect( body, `PHP fatal/parse error rendered by ${ url }` ).not.toMatch(
		FATAL_ERROR_PATTERN
	);
	expect(
		body,
		`PHP warning/notice/deprecation from this theme rendered by ${ url }`
	).not.toMatch( THEME_DIAGNOSTIC_PATTERN );
}

/**
 * Logs in as the wp-env administrator.
 *
 * The theme filters `login_redirect` (see `functions-personal-page.php`) to send
 * users to the personal page rather than the dashboard, so this waits for any
 * navigation away from `wp-login.php` instead of for a `wp-admin` URL.
 *
 * @param {import('@playwright/test').Page} page
 */
async function loginAsAdmin( page ) {
	await page.goto( '/wp-login.php' );
	await page.fill( '#user_login', ADMIN_USER );
	await page.fill( '#user_pass', ADMIN_PASSWORD );
	await page.click( '#wp-submit' );
	await page.waitForURL( ( url ) => ! url.pathname.endsWith( '/wp-login.php' ) );
}

module.exports = {
	ADMIN_USER,
	ADMIN_PASSWORD,
	THEME_PHP_FILES,
	FATAL_ERROR_PATTERN,
	THEME_DIAGNOSTIC_PATTERN,
	expectNoPhpDiagnostics,
	loginAsAdmin,
};
