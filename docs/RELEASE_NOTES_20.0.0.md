# pH7Builder 20.0.0 — Release Notes (Unreleased)

These are draft notes for the upcoming release. The v20.0.0 tag and release
package are not available yet.

## Breaking changes

- Requires PHP 8.3 or newer. pH7Builder 19.4.0 is the final release that
  supports PHP 8.2.

## Installer and dependency updates

- Upgrades the installation wizard to Smarty 5.8.5. This is an internal
  installer dependency; pH7Builder site themes continue to use PH7Tpl.
- Keeps FakerPHP and all 29 existing profile-generator locales. Invalid locale
  submissions are rejected server-side instead of falling back implicitly.

## Founder launch experience

- Replaces the time-limited admin getting-started list with a launch-readiness
  panel that remains visible until HTTPS, installer removal, production mode,
  online status, and cron URL protection are confirmed.
- Links each incomplete check to its relevant setting or documentation and
  makes clear that email, payment, backup, moderation, accessibility, and legal
  readiness still require human verification.

## Database and upgrade notes

The database schema remains `1.6.6`; no database migration is required. Before
deploying 20.0.0, upgrade the server runtime to PHP 8.3 or newer and confirm
that the locked dependencies support it. Sites that must remain on PHP 8.2
should stay on 19.4.0.

For source deployments, install the locked production dependencies with
`composer install`. For existing sites, preserve configuration and user data,
remove or block the installer, and clear application, template, and
browser/CDN caches after deployment.
