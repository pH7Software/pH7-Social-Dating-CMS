# pH7Builder 19.3.0 — Release Notes

Released on 22 September 2026, this minor security and maintenance release
hardens form validation and account recovery, makes two-factor and SMS sign-in
more reliable, bundles MaxMind's last Creative Commons GeoLite2 build and fixes
admin and search pages that failed with server errors. It includes all
dependency updates from 19.2.0.

## Security

- Validates each bundled form against its own ID. Previously the submitted form
  name decided which form's rules ran, so a crafted request could skip the
  intended form's validation, including its CSRF protection.
- Rejects CAPTCHA answers when no CAPTCHA code was generated. A client that never
  loaded the CAPTCHA image could pass the contact, invite, signup and login
  CAPTCHAs with an empty answer.
- Password reset links now open a form to choose a new password instead of
  emailing a generated one. Links expire within an hour, work once and stop
  working after any password change; only a digest of the token is stored.
- Two-factor verification sessions are tied to the account type, expire after
  ten minutes and recheck that the account may still sign in. The setup page
  offers an authenticator setup-key backup instead of a temporary six-digit
  "recovery code" download.
- A list sent where a form expects text now fails validation instead of
  crashing the form, and a list sent where a number is expected (`?id[]=7`)
  reads as 0, so it can no longer load record 1.

## Highlights

- SMS activation uses numeric, single-use codes valid for five minutes, with
  five attempts per code and per-session request limits.
- Bundles MaxMind's 24 December 2019 GeoLite2-City build, the last released
  under CC BY-SA 4.0, with its licence and notice files. The data is still
  historical, not a current location update.
- `install geoip db` in `_tools/pH7.sh` verifies or restores that build without
  a MaxMind account; a failed import keeps the previous database and notices.
- A missing or unreadable GeoIP database no longer interrupts login or signup.
  Configured country restrictions still fail closed.
- Fixes Module Manager, which failed with a server error, so modules can be
  installed and uninstalled again.
- `/video/admin` opens the YouTube API key settings instead of a server error.
- CSV member imports read each member from its own row. They previously read
  the header row and failed with a database error.
- Search and list pages for notes, blogs, photos, videos, forums, admins,
  affiliates, subscribers and messages no longer fail when the search term is
  sent as a list.
- An expired form now says why it was rejected instead of silently reloading.
- Search location fields keep apostrophes and ampersands and can be cleared.
- Clearing the whole country blocklist no longer logs a warning on every save,
  and logged exceptions are no longer followed by database-config warnings.
- Mailbox, profile and member-list pages no longer pass null to PHP string
  functions, which PHP 8.1 deprecated.
- Adds regression tests for each fix, including checks that every bundled form
  is validated against its own ID.

## Compatibility and upgrade

PHP 8.2+, MySQL 8.0+, schema `1.6.6` and the locked Composer dependencies are
unchanged. No database migration is needed from 19.2.0.

**Custom modules:** replace `\PFBC\Form::isValid($_POST['submit_…'])` with the
form's literal ID, for example `\PFBC\Form::isValid('form_login')`. Passing the
request value lets a client choose which form's rules are checked.

**After deployment:** previously issued password reset links are rejected, and
pending SMS or two-factor verifications must restart. Members who downloaded a
six-digit two-factor file should create a setup-key backup instead; the old
file cannot restore access.

Back up first and follow the [Upgrade Guide](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.0/docs/UPGRADING.md).
Deploy the complete package, preserve configuration and user data, remove the
installer on existing sites, and clear caches. Source deployments must install
dependencies from the committed lock file. Preserve any newer GeoLite2 database
you maintain yourself; see the
[GeoIP database instructions](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.0/_protected/framework/Geo/Ip/update-geo-database-version.txt).

The `pH7Builder-v19.3.0.zip` asset includes production dependencies. Verify its
attached SHA-256 checksum, then use the
[Quick Start](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.0/docs/QUICK_START.md) and
[Launch Checklist](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.0/docs/LAUNCH_CHECKLIST.md).
SMTP delivery, SMS, GeoNames and payments still require checks with your own
provider configuration before going live.

[All changes since 19.2.0](https://github.com/pH7Software/pH7-Social-Dating-CMS/compare/v19.2.0...v19.3.0)
