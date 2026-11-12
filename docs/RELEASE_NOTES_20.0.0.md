# pH7Builder 20.0.0 — Release Notes

## Breaking changes

- Requires PHP 8.3 or newer. Xefi Faker v1.5.4 currently accepts PHP 8.3–8.5
  only, so Composer does not yet install this release on PHP 8.6 or later.
  pH7Builder 19.4.0 is the final release that supports PHP 8.2.
- Replaces FakerPHP with Xefi Faker. The profile generator now offers the
  seven locales for which Xefi publishes locale data: American (`en_US`),
  British (`en_GB`), French (`fr_FR`), French Belgian (`fr_BE`), French Swiss
  (`fr_CH`), German (`de_DE`), and Spanish (`es_ES`). Generated member,
  affiliate, and subscriber profiles remain available wherever the site owner
  enables them, including production sites.

## Database and upgrade notes

The database schema remains `1.6.6`; no database migration is required. Before
deploying 20.0.0, upgrade the server runtime to PHP 8.3–8.5. Sites that must
remain on PHP 8.2 should stay on 19.4.0.

For source deployments, install the locked production dependencies with
`composer install`. For existing sites, preserve configuration and user data,
remove or block the installer, and clear application, template, and
browser/CDN caches after deployment.
