# Upgrading pH7Builder

Automatic in-place upgrades are currently unavailable. Upgrade a staging copy
manually, verify it, and only then repeat the reviewed procedure in production.

## Unreleased authentication maintenance

Password reset links now open a form to choose a new password. They expire within
one hour, work once and become invalid after any password change. Passwords are
no longer generated or emailed. After deployment, request a new link: previously
issued reset links are intentionally rejected. Successful resets return to normal
sign-in, including any configured two-factor authentication. No schema change is
required; only a password-bound digest of the reset token is stored.

SMS activation now uses numeric, single-use codes valid for five minutes, with
five attempts per code. Requests in the same session are limited to one per minute
and five per 15 minutes. New installations use six digits; existing configurations
may retain a length from four to eight. Pending SMS verification sessions must
restart after deployment. Successful phone activation returns to the normal login
page, where account restrictions and two-factor authentication still apply.
Gateway failures leave no usable code; check the configured SMS provider if
delivery continues to fail.

No database migration is required. Deploy the matching application and framework
files together. A sign-in waiting for a two-factor code during deployment must
restart from the login page. New verification sessions expire after ten minutes
and are tied to their account type; completing verification clears them.

The two-factor setup page now offers an **authenticator setup-key backup**, not
a one-time recovery code. Previously downloaded six-digit files are temporary
codes and must not be relied on for recovery. While signed in, use **Back up
authenticator setup key** and keep the new file offline in a secure place. To
restore access, import the key into an authenticator app, then sign in normally
with your password and its current code. Never share this key: it can generate
your sign-in codes. No existing authenticator enrolment changes.

## Unreleased GeoIP maintenance

Changes after 19.2.0 replace the bundled 3 December 2019 GeoLite2 database with
MaxMind's last Creative Commons build (24 December 2019) and its original
notices. These changes are not in the published 19.2.0 package. The data is still
historical; this is not a current-location-data update. No database migration
is needed. Preserve any newer GeoLite2 database you maintain yourself.

In a Git checkout, `install geoip db` in `_tools/pH7.sh` verifies or restores the
pinned build without MaxMind credentials. Custom imports require PHP and
Composer dependencies for validation; archives must include their notice files.
Failed installations preserve the previous database and notices. See the
[GeoIP database instructions](../_protected/framework/Geo/Ip/update-geo-database-version.txt).

An unreadable or missing GeoIP file no longer interrupts ordinary login or
signup; PHP's error log records a restoration message. Configured country
restrictions still fail closed when the database is unavailable; the admin
panel remains accessible for recovery. Country and city suggestions remain
unavailable until the database is restored. Search location fields also keep
apostrophes and ampersands intact and allow owners and members to clear a filter.

## 19.2.0 dependency-maintenance release

pH7Builder 19.2.0 updates the locked PHP dependencies and adds offline SDK
compatibility checks. PHP 8.2+, MySQL 8.0+ and schema `1.6.6` are unchanged.
No database migration is needed from 19.1.0. Bootstrap, jQuery, jQuery UI and
the installer dependency are unchanged.

Before upgrading, check `php --ri maxminddb` using the PHP installation that
serves your site. If the optional compiled `ext-maxminddb` extension is present,
upgrade it to `>=1.14.0 <2.0.0`; older versions conflict with the updated reader.
The bundled pure-PHP reader does not require installing this extension.

The 19.2.0 reader update does not refresh its bundled GeoLite2 location data
(3 December 2019). The later 24 December build and credential-free restore
command described above are unreleased changes. The
[GeoIP database instructions](../_protected/framework/Geo/Ip/update-geo-database-version.txt)
explain how to restore data from a matching release package and why newer GeoLite
data cannot be bundled. Preserve any newer database you installed yourself when
replacing application files.

The base and premium footers now show a plain, versioned pH7Builder link to
GitHub instead of the site name and GeoLite2 line. The existing branding
visibility setting still applies. MaxMind attribution is on the Legal Notice
page and in `COPYRIGHT.md`; retain it when merging customised legal pages.

Back up and test a staging copy. Deploy the complete 19.2.0 package while
preserving local configuration, uploads, custom modules/themes, language packs
and credentials. Source deployments must run `composer install --no-dev
--prefer-dist --optimize-autoloader` from the new lock file. Do not run
`composer update` on the live site or reuse an older vendor directory. Do not
rerun the installer on an existing site; remove `_install` before reopening it.

Clear application and browser/CDN caches. Check signup, login, admin navigation,
email delivery, SMS, storage and a sandbox payment for the providers you use.
Custom Twilio integrations should review its upstream changes, including the
retired WhatsApp Senders v1 endpoints and changes to preview APIs; the bundled
SMS provider uses the 2010 Messages API, not those endpoints. See the
[19.2.0 release notes](RELEASE_NOTES_19.2.0.md) for dependency versions and sources.
Earlier installations must also follow the applicable guidance below.

## 19.1.0 maintenance release

pH7Builder 19.1.0 fixes member-login errors for invalid credentials, restores
nested admin menus, and improves dialog and tooltip contrast. It also avoids a
PHP deprecation when a profile has no birth date. PHP 8.2+ and MySQL 8.0+
requirements are unchanged. The SQL schema remains `1.6.6`; no database
migration is needed from 19.0.1.

Back up and upgrade a staging copy first. Deploy the complete tagged 19.1.0
package while preserving local configuration, uploads, custom modules/themes,
language packs, and credentials. The release ZIP includes production
dependencies; source deployments must run `composer install --no-dev
--prefer-dist --optimize-autoloader`. Do not rerun the installer on an existing
site; remove the deployed `_install` directory before reopening it.

Clear application caches in Admin → Tools → Caches, purge any CDN asset cache,
and refresh browser assets. Deploy PHP, CSS and JavaScript together. Test valid and invalid
member logins, signup, Stay signed in, and the nested Mod and Tools → Info menus
on desktop and mobile. Review custom overrides of login processing or shared
theme CSS, navigation templates and JavaScript. Earlier installations must also
follow the applicable guidance below.

## 19.0.1 patch release

pH7Builder 19.0.1 fixes form rendering, Ajax retries, age sliders, and recipient
and city autocomplete. PHP 8.2+ and MySQL 8.0+ requirements are unchanged. The
SQL schema remains `1.6.6`; no database migration is needed from 19.0.0.

Back up and upgrade a staging copy first. Deploy the complete tagged 19.0.1
package, preserving local configuration, uploads, custom modules and themes,
language packs, and gateway credentials. The release ZIP includes production
dependencies; source deployments must run `composer install --no-dev
--prefer-dist --optimize-autoloader` to install the updated locked Symfony
patches. Do not rerun the installer on an existing site; remove the deployed
`_install` directory before reopening it.

Clear application caches in Admin → Tools → Caches, purge any CDN asset cache,
and refresh browser assets. Deploy the PHP, CSS, and JavaScript files together.
Review custom themes that override the bundled form styles or password-toggle
markup. Test signup, login, agreement checkboxes, age sliders, Ajax form retry,
recipient suggestions, and city/state/postcode selection on desktop and mobile.
External SMTP, GeoNames, and payment services still require deployment-specific
checks. Sites older than 19.0.0 must also follow the applicable guidance below.

## 19.0.0 major release

pH7Builder 19.0.0 is a code-only security, compatibility, and usability release
over 18.6.2. The SQL schema remains `1.6.6`, so an installation already running
18.6.0, 18.6.1, or 18.6.2 needs no database migration. Deploy the tagged 19.0.0
package without overwriting local configuration, uploaded data, custom modules,
custom themes, language packs, or gateway credentials. Reinstall dependencies
from the committed lock file when deploying from source, clear application
caches, and complete the post-upgrade checks below before reopening the site.

Sites older than 18.6.0 must still apply every applicable intermediate migration
in order. In particular, an 18.5.1 database requires the reviewed
`18.5.1-18.6.0` migration before the 19.0.0 application is used.

This release tightens installer access, request authorization, output escaping,
template-path validation, and ownership checks. Do not restore older copies of
the changed application or framework files after deployment. Review custom
themes and modules for the same output-encoding and ownership boundaries before
re-enabling them.

## 18.6.2 patch release

pH7Builder 18.6.2 is a code-only security-hardening patch over 18.6.1. It does
not change the SQL schema, so an installation already running 18.6.0 or 18.6.1
needs no database migration. Deploy the 18.6.2 files without overwriting local
configuration, uploaded data, custom modules, custom themes, or gateway
credentials. Reinstall dependencies from the committed lock file when
deploying from source, clear application caches, and verify every installed
language before reopening the site.

Sites older than 18.6.0 must still follow every applicable intermediate path
below. In particular, an 18.5.1 database still requires the reviewed
`18.5.1-18.6.0` migration before the 18.6.2 application is used.

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

1. Deploy the tagged 18.6.2 source without overwriting local configuration,
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
