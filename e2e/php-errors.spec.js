/**
 * Asserts the theme's front-end surfaces render without PHP diagnostics.
 *
 * This theme is a hybrid: `templates/` ships block templates for `single`,
 * `archive`, `index`, `page` and `404`, and those take precedence over the
 * same-named PHP files. The surfaces that still render the theme's own PHP
 * templates are therefore the front page (`front-page.php`, which has no
 * `front-page.html` counterpart) and search (`search.php`) — both of which pull
 * in `header.php`, `footer.php` and files from `template-parts/`.
 *
 * The block-template surfaces are asserted too: they still load
 * `functions.php` and everything it requires from `theme-config/`.
 */

const { test, expect } = require( '@playwright/test' );
const { expectNoPhpDiagnostics } = require( './helpers' );

/*
 * KNOWN DEFECT — the two `test.fail()` cases below are expected to fail. Do not
 * "fix" them by weakening `THEME_DIAGNOSTIC_PATTERN`.
 *
 * `get_soli_fp_info()` (theme-config/admin/functions.php:59) builds its array
 * from the rows of the `soli_imaging` table where `type="frontpage"`. On any
 * install without such a row it returns an empty array, and three call sites
 * read keys out of it without guarding:
 *
 *   - front-page.php:12,22,30 — `frontpage_background`, `frontpage_button_link`,
 *     `frontpage_subtitle`
 *   - template-parts/search-results.php:6,15 — `frontpage_background`, plus
 *     `$image[0]` on the `false` that `wp_get_attachment_image_src()` returns
 *   - theme-config/login/functions.php:17,20,56 — the same pattern again
 *
 * Enabling `WP_DEBUG` for the tests environment is what made these visible.
 * Once `get_soli_fp_info()` returns its keys with defaults (and the `$image[0]`
 * dereferences are guarded), turn these back into plain `test()` calls.
 */

test.describe( 'renders without PHP errors', () => {
	test.fail( 'on the front page (front-page.php)', async ( { page } ) => {
		const response = await page.goto( '/' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test.fail( 'on a search results page (search.php)', async ( { page } ) => {
		const response = await page.goto( '/?s=hello' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on a single post', async ( { page } ) => {
		// wp-env seeds "Hello world!" as post 1.
		const response = await page.goto( '/?p=1' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on a category archive', async ( { page } ) => {
		const response = await page.goto( '/?cat=1' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on a 404 page', async ( { page } ) => {
		const response = await page.goto( '/?p=999999' );
		expect( response.status() ).toBe( 404 );
		await expectNoPhpDiagnostics( page );
	} );
} );
