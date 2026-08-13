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
 * The front page and search templates both read their header artwork and call
 * to action out of `get_soli_fp_info()` (theme-config/admin/functions.php),
 * which returns the `soli_imaging` rows of type "frontpage" keyed by setting
 * name. A wp-env install has never saved that admin screen, so there are no
 * such rows and the function falls back to its empty-string defaults — which is
 * exactly the path these tests exercise. `wp_get_attachment_image_src()`
 * returns `false` for an empty attachment id, hence the `$image` guards around
 * every `$image[0]` in those templates.
 */

test.describe( 'renders without PHP errors', () => {
	test( 'on the front page (front-page.php)', async ( { page } ) => {
		const response = await page.goto( '/' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on a search results page (search.php)', async ( { page } ) => {
		const response = await page.goto( '/?s=hello' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on an empty search (template-parts/search-none.php)', async ( {
		page,
	} ) => {
		// search.php picks search-none over search-results on an empty term,
		// not on zero results.
		const response = await page.goto( '/?s=' );
		expect( response.status() ).toBe( 200 );
		await expectNoPhpDiagnostics( page );
	} );

	test( 'on the login screen', async ( { page } ) => {
		// theme-config/login/functions.php paints the login background from the
		// same front page settings.
		const response = await page.goto( '/wp-login.php' );
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
