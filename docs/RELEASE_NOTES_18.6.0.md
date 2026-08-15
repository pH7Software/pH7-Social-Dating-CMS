# pH7Builder 18.6.0 — Release Notes

## Highlights

- Requires PHP 8.2+ and MySQL 8.0+ for fresh installations. MariaDB and older
  MySQL versions are not verified or supported by this release.
- Removes the stale PostgreSQL installer schema; no audited PostgreSQL install
  or upgrade path is claimed for 18.6.0.
- Hardens the browser installer with an out-of-band access token, resumable
  progress, requirement reporting, request tokens,
  database ports and strict-mode schema import, recoverable step failures,
  configuration writes, admin creation, and installer cleanup.
- Fixes public signup navigation and the MySQL 8 query failure in Notes.
- Aligns new-install and 18.5.1 upgrade schemas for full-Unicode wall posts.
- Moves PayPal fulfillment to a verified asynchronous IPN callback backed by a
  persisted checkout and unique transaction ID, so cross-site cookie policy and
  duplicate notifications cannot lose or repeat a membership upgrade.
- Supports server-managed SMTP through `PH7_MAILER_DSN`, while retaining the
  existing sendmail path when no DSN is configured.
- Tightens web-server examples so internal source, configuration, logs, tests,
  and unsupported PHP entry points are not publicly served.
- Narrows packaged write permissions to runtime data and the specific existing
  files supported by admin configuration/editor screens; executable
  configuration PHP and source directories remain read-only.
- Refreshes setup guidance with a production Quick Start and an operational
  Launch Checklist.
- Uses a neutral 0% tax rate for new installations and explains that owners
  must confirm the applicable checkout rate for their business and jurisdiction.
- Retires the defunct Metacafe video provider. YouTube, Vimeo, and Dailymotion
  remain available.
- Retires and removes the bundled CKEditor 3 and TinyMCE 3 assets. Existing
  editor fields now render as plain textareas.

## Upgrade notes

1. Run `SELECT VERSION();` and upgrade the database server to MySQL 8.0 or
   newer before deploying this release. Do not assume MariaDB compatibility.
2. Back up the database, application configuration, uploaded media, and local
   customizations. Verify the backup can be restored.
3. Test the deployment against a staging copy before production.
4. Apply the `18.5.1-18.6.0` upgrade migration for the supported direct path.
   Sites older than 18.5.1 must follow each documented intermediate migration;
   do not skip schema versions.
5. Reinstall Composer dependencies from the committed lock file, clear
   application caches, and test signup, login, Notes, uploads, email, cron, and
   payments.
6. Confirm production debug display is off and `_install` is absent or blocked.
7. Preserve locally configured payment/module files. New-install payment
   defaults are disabled and in sandbox mode; never overwrite a live gateway
   configuration with those defaults.
   The migration must be applied before PayPal is re-enabled. Complete a new
   sandbox checkout and verify the callback before accepting live payments;
   pre-upgrade checkouts are not migrated.
   Stripe remains unavailable in 18.6.0 even if an upgraded config says it is
   enabled: the bundled legacy token/Charges flow is not SCA-ready. Keep the
   setting disabled until the integration is migrated to Stripe Checkout
   Sessions or Payment Intents.
   The bundled legacy 2Checkout integration is also fail-closed until it is
   migrated to the 2Checkout API 6.0.
   Braintree remains available, but the provider has scheduled its
   [Drop-in UI](https://developer.paypal.com/braintree/docs/guides/drop-in/overview/javascript/v3)
   for deprecation on September 1, 2026 and end of support on September 1,
   2027. Plan a supported checkout migration before those dates.
8. Configure the web server's canonical host. Use `PH7_CANONICAL_HOST` when the
   public host differs from `SERVER_NAME`; trust forwarded protocol headers only
   with `PH7_TRUST_PROXY_HEADERS=1` behind a proxy that strips client values.
9. Cookies are host-only by default. Cross-subdomain deployments must opt in
   with a validated `PH7_COOKIE_DOMAIN` and test the transition.
10. Replace any existing Metacafe video links with a supported video source;
    this release no longer accepts or renders the retired provider.
11. The existing `wysiwygEditorForum` database setting is preserved but ignored.
    Stored content is not rewritten. Keep the plain textarea unless a maintained
    editor is integrated with the existing server-side HTML sanitization.

The MySQL 8.0 minimum is an intentional compatibility change, not a silent
upgrade. Operators unable to move from older MySQL or MariaDB should remain on
their current supported deployment until they have tested a migration.

Fresh installation is supported through the authenticated browser installer.
The legacy `setup:install` CLI command is disabled because it lacked equivalent
validation and transaction guarantees.

## Release verification before publication

- [x] Fresh install completes on the release package with PHP 8.2 and MySQL 8.
- [x] Direct upgrade from an unmodified 18.5.1 database completes and preserves
      user data.
- [x] PHPUnit, static analysis, Composer validation/audit, installer validation,
      and PHP syntax checks pass.
- [x] Apache and nginx smoke tests cover public pages, admin login, protected
      paths, uploads, and error responses.
- [x] The GitHub release archive includes all production dependencies or the
      installation instructions clearly require Composer.
- [x] Version constants, installer metadata, Git tag, release title, and this
      document all say `18.6.0`.

## Post-publication operational step

- [ ] Superseded by the 18.6.1 operational step. The feed was not advanced for
      18.6.0, so no installed site was directed to a missing or incomplete
      patch; publish and verify 18.6.1 before enabling its update alert.
