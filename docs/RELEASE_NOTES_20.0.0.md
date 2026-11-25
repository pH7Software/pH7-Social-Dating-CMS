# pH7Builder 20.0.0 — Release Notes

Released on 25 November 2026, pH7Builder 20.0.0 makes installation, migration,
security checks, and release verification more predictable while modernising
the supported PHP foundation.

## Breaking changes

- Drops PHP 8.2 support and supports PHP 8.3 through 8.5. pH7Builder 19.4.0 is
  the final release for PHP 8.2.
- Removes the deprecated Braintree Drop-in checkout. Operators using Braintree
  must migrate to a supported payment integration before upgrading.

## Installer and dependency updates

- Requires Smarty 5.8.5 or newer for the installation wizard and currently
  locks Smarty 5.9.0. This is an internal installer dependency; pH7Builder site
  themes continue to use PH7Tpl.
- Replaces FakerPHP with the PHP 8.3-native Xefi Faker implementation while
  retaining all 29 existing profile-generator locales. Invalid locale
  submissions remain rejected server-side instead of falling back implicitly.
- Keeps Clickatell SMS support while replacing its unmaintained GPL wrapper
  with the existing MIT-licensed HTTP client and Clickatell's current API.

## Reliability and security

- Adds a release gate that verifies a packaged fresh installation, the
  19.4.0-to-20.0.0 migration, a restore rehearsal, static analysis, browser
  journeys, production dependencies, and release metadata.
- Publishes a SHA-256 checksum, SPDX SBOM, and build manifest with the release
  ZIP so operators can verify its origin and contents.
- Adds a read-only operator health report for runtime compatibility, writable
  paths, cron, mail, caches, migrations, and unsafe production settings.
- Strengthens output encoding, CSRF comparison, FFmpeg argument validation,
  compiler transport security, module permissions, and IP-ban permissions.
- Updates jQuery to 3.8.0 to address the upstream selector ReDoS issue.

## Migration and product experience

- Adds safe CSV migration previews and a read-only, resumable UNA 14 migration
  adapter. Other source versions are not claimed as compatible.
- Keeps optional profile generation and milestone celebration behind module
  feature gates for fresh installations and upgrades.
- Improves mobile splash and signup layouts, defers splash video loading, keeps
  empty profile-rating pages usable, and avoids loading maps without an API key.

## Database and upgrade notes

The SQL schema advances to `1.6.7`. Existing installations must apply
`_repository/upgrade/19.4.0-20.0.0/data/sql/MySQL/upgrade.sql` so the profile
generator and milestone celebration modules can be controlled from the module
manager. It widens the module folder identifier to store the milestone module's
full name. The migration is idempotent and preserves both modules' existing
enabled behavior.

Before deploying 20.0.0, use PHP 8.3, 8.4, or 8.5. The locked Xefi Faker
dependency does not yet support PHP 8.6, so the installer rejects that runtime
instead of claiming unverified compatibility. Sites that must remain on PHP
8.2 should stay on 19.4.0.

For source deployments, install the locked production dependencies with
`composer install`. For existing sites, preserve configuration and user data,
remove or block the installer, and clear application, template, and
browser/CDN caches after deployment. Automatic in-place upgrades remain
unavailable: rehearse the documented manual upgrade on staging and verify a
real restore before changing production. The bundled legacy Stripe flow stays
disabled until it is migrated and independently verified. See the
[automated release gate](RELEASE_PROCESS.md#automated-release-gate) and
[launch checklist](LAUNCH_CHECKLIST.md) for the tested boundaries.
