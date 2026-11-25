# pH7Builder 20.0.0 — Release Notes (Unreleased)

These are draft notes for the upcoming release. The v20.0.0 tag and release
package are not available yet.

## Breaking changes

- Requires PHP 8.3 or newer. pH7Builder 19.4.0 is the final release that
  supports PHP 8.2.
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

## Database and upgrade notes

The SQL schema advances to `1.6.7`. Existing installations must apply
`_repository/upgrade/19.4.0-20.0.0/data/sql/MySQL/upgrade.sql` so the profile
generator and milestone celebration modules can be controlled from the module
manager. It widens the module folder identifier to store the milestone module's
full name. The migration is idempotent and preserves both modules' existing
enabled behavior.

Before deploying 20.0.0, upgrade the server runtime to PHP 8.3 or newer and
confirm that the locked dependencies support it. Sites that must remain on PHP
8.2 should stay on 19.4.0.

For source deployments, install the locked production dependencies with
`composer install`. For existing sites, preserve configuration and user data,
remove or block the installer, and clear application, template, and
browser/CDN caches after deployment. The release candidate must pass the
[automated release gate](RELEASE_PROCESS.md#automated-release-gate), including
a packaged fresh installation, the 19.4.0 migration, and a restore rehearsal.
