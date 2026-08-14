[![version](https://img.shields.io/github/package-json/v/JoranOut/wp-theme-soli/master?label=version&color=3858e9)](https://github.com/JoranOut/wp-theme-soli/releases)
[![nightly](https://img.shields.io/github/v/release/JoranOut/wp-theme-soli?include_prereleases&label=nightly&color=fb8817)](https://github.com/JoranOut/wp-theme-soli/releases)
[![tested up to](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fapi.wordpress.org%2Fcore%2Fversion-check%2F1.7%2F&query=%24.offers%5B0%5D.current&label=tested%20up%20to&prefix=WP%20&color=40a8af)](https://wordpress.org/download/releases/)
[![requires](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FJoranOut%2Fwp-theme-soli%2Fmaster%2Fpackage.json&query=%24.wordpress.requiresAtLeast&label=requires&prefix=WP%20&color=40a8af)](https://wordpress.org/download/releases/)
[![node](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FJoranOut%2Fwp-theme-soli%2Fmaster%2Fpackage.json&query=%24.engines.node&label=node&color=43853d)](https://nodejs.org)

# WP Theme Soli

<!-- Machine-readable markers. publish.js reads the theme name to name the zip,
     and the nightly workflow rewrites the version here when packaging a build.
     Kept in a comment because a single tilde renders as strikethrough on GitHub;
     the badges above are the human-readable version. Do not reformat.
~Plugin Name: wp-theme-soli~
~Current Version:1.4.3~
-->

Main public-facing WordPress theme for [soli.nl](https://soli.nl).

## Requirements

- WordPress 6.9 or newer. The e2e suite runs against the newest patch of that
  branch and against the newest WordPress release, and both release workflows
  stamp the theme updater's advertised range from those two numbers. The floor
  is declared once, as `wordpress.requiresAtLeast` in `package.json`.
- PHP 8.4+

## Development

### Local environment

```bash
npm install
npm run env:start
```

Local site runs at `http://localhost:8908` (admin: admin/password).

### Testing

```bash
npm run test:e2e
```

### Building a release

```bash
npm run publish
```

## License

GPL-3.0-or-later
