# pH7Builder 19.3.3 — Release Notes

Released on 2 November 2026, this security and maintenance patch updates
Symfony MIME to close CVE-2026-45070 and prevents custom GeoIP databases from
being installed with licence notices from a different MaxMind release.

## Security and maintenance

- Updates `symfony/mime` from 7.4.18 to 7.4.22, the patched release for the
  MIME parameter-name header-injection vulnerability described in
  [CVE-2026-45070](https://symfony.com/cve-2026-45070).
- Updates the MIME dependency's Symfony polyfills to their compatible locked
  releases. Composer audits report no remaining known vulnerabilities.
- Requires `LICENSE.txt`, `COPYRIGHT.txt` and `README.txt` from the same release
  beside an imported GeoLite2-City `.mmdb`. A bare database is rejected before
  installation, and a stale bundled Creative Commons licence is removed when
  the imported release does not supply it.
- Keeps the Production Quick Start as the canonical release-version source and
  pins Ubuntu GitHub Actions jobs to Ubuntu 24.04 for reproducible CI runs.

The bundled 24 December 2019 GeoLite2-City database, its checksum and its
CC BY-SA 4.0 notices are unchanged.

## Compatibility and upgrade

PHP 8.2+, MySQL 8.0+ and schema `1.6.6` are unchanged. No database migration is
needed from 19.3.2. There are no application configuration or custom-theme
changes in this release.

Deploy the complete package, preserve configuration and user data, remove the
installer on existing sites, and clear application, template and CDN caches.
Source checkouts should run `composer install` from the tagged tree so the fixed
dependency lock is applied.

The `pH7Builder-v19.3.3.zip` asset includes production dependencies. Verify its
attached SHA-256 checksum, then use the [Quick Start](QUICK_START.md) and
[Launch Checklist](LAUNCH_CHECKLIST.md).

[All changes since 19.3.2](https://github.com/pH7Software/pH7-Social-Dating-CMS/compare/v19.3.2...v19.3.3)
