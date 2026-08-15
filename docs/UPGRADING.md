# Upgrading pH7Builder

Automatic in-place upgrades are currently unavailable. Upgrade a staging copy
manually, verify it, and only then repeat the reviewed procedure in production.

## 18.6.1 patch release

pH7Builder 18.6.1 is a code-only patch over 18.6.0. It does not change the SQL
schema, so an installation already running 18.6.0 needs no database migration.
Deploy the 18.6.1 files without overwriting local configuration, uploaded data,
custom modules, custom themes, or gateway credentials. Reinstall dependencies
from the committed lock file when deploying from source, clear application
caches, and test signup through login before reopening the site.

Sites older than 18.6.0 must still follow every applicable intermediate path
below. In particular, an 18.5.1 database still requires the reviewed
`18.5.1-18.6.0` migration before the 18.6.1 application is used.

## 18.6.0 compatibility change

pH7Builder 18.6.0 requires MySQL 8.0 or newer. Older MySQL versions and MariaDB
are unverified and unsupported for this release. Check before deploying code:

```sql
SELECT VERSION();
```

If the result is not MySQL 8.0+, stop. Migrate and test the database server
first, or remain on the current deployment. This requirement applies to
upgrades as well as fresh installations.

The stale PostgreSQL installer schema has been removed because this release has
no audited PostgreSQL installation or upgrade path. Existing PostgreSQL
deployments are uncertain and must not be upgraded with the MySQL migration.

## Before every upgrade

1. Read the release notes for the target and every intermediate version.
2. Put the site in maintenance mode.
3. Record the current Git tag, PHP version, MySQL version, database prefix,
   enabled modules, theme, cron entries, and payment mode.
4. Back up the database, `_constants.php`, `_protected/app/configs/`, `data/`,
   custom modules/themes, every locally edited module `config/config.ini`, and
   any other local changes. In particular, preserve payment, affiliate, SMS,
   and video service settings and credentials.
5. Restore those backups into a separate staging environment. An untested
   backup is not a rollback plan.

A MySQL backup example is:

```console
sudo install -d -m 0700 -o deploy -g deploy /var/backups/ph7builder
mysqldump --single-transaction --routines --triggers --default-character-set=utf8mb4 -u ph7builder -p ph7builder > /var/backups/ph7builder/ph7builder-before-upgrade.sql
```

Replace the example account names as needed. Keep the dump outside the public
web root and protect it as production data.

## Direct 18.5.1 → 18.6.x path

1. Deploy the tagged 18.6.1 source without overwriting local configuration,
   uploads, custom modules, custom themes, or gateway credentials. Merge the
   safer payment defaults deliberately; do not replace a live payment config
   with the release template.
2. The versioned release ZIP already includes its locked production
   dependencies. If you deploy from the tagged source checkout instead, install
   them from the release root:

   ```console
   composer install --no-dev --prefer-dist --optimize-autoloader
   ```

3. Review
   `_repository/upgrade/18.5.1-18.6.0/data/sql/MySQL/upgrade.sql` before running
   it. The committed migration uses the default `ph7_` table prefix. If the
   installed configuration uses another prefix, create and review a copy with
   the exact installed prefix; do not run the default-prefix SQL unchanged.
4. For an installation that uses the default prefix, apply the migration once:

   ```console
   mysql -u ph7builder -p ph7builder < _repository/upgrade/18.5.1-18.6.0/data/sql/MySQL/upgrade.sql
   ```

   This migration creates the persisted, replay-protected payment transaction
   table required by the 18.6.0 PayPal IPN flow. Apply it before re-enabling
   PayPal, then start a new sandbox checkout; a checkout opened on 18.5.1 does
   not contain the new persisted reference and must not be reused.
   Stripe is fail-closed in 18.6.0 even when an existing config has it enabled.
   Keep it disabled until the legacy token/Charges flow is migrated to Stripe
   Checkout Sessions or Payment Intents.
   The bundled legacy 2Checkout flow is also fail-closed until it is migrated
   to the 2Checkout API 6.0.

5. Ensure the web virtual host has the exact canonical domain. On upgraded
   installations, `index.php` normalizes the legacy generated URL logic to
   `SERVER_NAME`. If the public hostname differs from that server value, set
   `PH7_CANONICAL_HOST=example.com` in the PHP web-process environment. Set
   `PH7_TRUST_PROXY_HEADERS=1` only behind a trusted proxy that strips incoming
   forwarded headers and supplies its own.
6. Cookies are host-only by default in 18.6.0. If the existing deployment
   intentionally shares authentication across subdomains, set the validated
   parent `PH7_COOKIE_DOMAIN=example.com` before rollout and test login/logout
   on every host. Otherwise remove any legacy parent-domain setting and clear
   old browser cookies during the maintenance window.
7. Reapply group-write access to the application config plus the payment,
   affiliate, SMS, and video module config files when those admin editors are
   used. If the built-in File/Page editors are used, grant group write only to
   their existing mail, static-page, theme, banned-list, suggestion-list, and
   route target files as shown in the [Quick Start](QUICK_START.md#4-set-narrow-filesystem-permissions).
   Keep their directories and executable configuration PHP read-only to PHP.
8. Remove the newly deployed installer tree before reopening the site:

   ```console
   sudo rm -rf -- /var/www/ph7builder/_install
   ```

   Replace the example document root first, then verify that exact `_install`
   path is absent. Do not run the command against a variable or broader path.
9. Clear application caches in Admin → Tools → Caches.
10. Confirm the site and admin panel report the deployed target version, then
    test signup, login, Notes, profile editing, uploads, password reset, email,
    cron, memberships, and payment callbacks.
11. Inspect application, PHP-FPM, and web-server logs before taking the site out
   of maintenance mode.

Metacafe is retired and is no longer accepted as a video provider. Replace any
existing Metacafe entries with a supported YouTube, Vimeo, or Dailymotion source
before reopening the site. There is no automatic content migration.

The bundled CKEditor 3 and TinyMCE 3 integrations are retired. Forum and custom
editor fields render as plain textareas in 18.6.0, and the existing
`wysiwygEditorForum` database setting is retained but ignored. Stored content is
unchanged. The legacy public editor assets have been removed; use a maintained
editor and preserve the server-side HTML sanitization if rich editing is needed.

Sites older than 18.5.1 must apply every documented intermediate migration in
order. Do not skip schema versions or run the 18.5.1 migration against an
unknown schema.

The former `setup:install` CLI path is disabled in 18.6.0 because it did not
provide the browser installer's validation and transactional safeguards. Fresh
installation is supported through `/_install/` after creating its out-of-band
access token with `php _install/create-install-token.php`.

## Rollback

If verification fails, keep the site closed, restore the previous application
files and database together, restore the previous dependency set, clear
caches, and confirm the recorded previous version. Do not combine old code
with a partially migrated database.

The maintained manual upgrade guide is also linked from the admin update
notice: <https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/docs/UPGRADING.md>.
