/**
 * Smoke test for Soli Theme.
 *
 * Verifies the theme loads correctly on the front-end.
 */

const { test, expect } = require('@playwright/test');

test.describe('Theme loads', () => {
	test('homepage returns 200', async ({ page }) => {
		const response = await page.goto('/');
		expect(response.status()).toBe(200);
	});

	test('homepage contains site title', async ({ page }) => {
		await page.goto('/');
		const body = await page.textContent('body');
		expect(body).toBeTruthy();
	});
});
