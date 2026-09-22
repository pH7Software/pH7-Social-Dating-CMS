# pH7Builder 19.3.1 — Release Notes

Released on 22 September 2026, this patch release fixes form, comment and
affiliate issues found while verifying 19.3.0. It includes all security and
maintenance changes from 19.3.0.

## Fixes

- Shows a failed submission's input and errors only on the page it returns to.
  Edit forms share one ID for every record, so after a failed save on one
  record, opening another showed the first one's input, and saving it wrote
  that input onto the wrong record.
- Rejects select, radio and checkbox values the form never offered. A crafted
  value previously reached the database; with strict MySQL, the membership
  form returned a server error instead of a validation message.
- Shows the "not found" page for comment URLs with an unknown content type,
  instead of a server error.
- Keeps members and admins signed in when they open the affiliate programme
  pages. Those pages now link to the affiliate login page instead of embedding
  its form, which switches the browser to an affiliate account.
- Lets affiliates save their account without a description, which signup never
  asks for. A description, when given, must still be 20 to 4000 characters.
- Wraps newsletter messages in an element that accepts paragraphs, removing the
  empty paragraph at the top of each newsletter and the "Unexpected end tag"
  warning logged once per recipient.
- Reports CSV imports as "N users have been successfully added."
- Stops form validation from passing a missing field to string functions in the
  length, date and username rules, which PHP 8.1 deprecates and PHP 9 is
  expected to reject, and from logging warnings when a request omits a file
  field.
- Adds regression tests for each fix.

## Compatibility and upgrade

PHP 8.2+, MySQL 8.0+, schema `1.6.6` and the locked Composer dependencies are
unchanged. No database migration is needed from 19.3.0.

**Custom forms:** option elements now reject values that are not among the
options they rendered. Forms that add options in the browser with JavaScript
must add them on the server too. A failed submission's input and errors are
shown once, so a page that renders the same form twice shows them only in the
first copy.

**Custom themes:** overrides of the newsletter message mail template or the
affiliate `login.inc.tpl` should apply the same changes.

Back up first and follow the [Upgrade Guide](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.1/docs/UPGRADING.md).
Deploy the complete package, preserve configuration and user data, remove the
installer on existing sites, and clear application, template and CDN caches.
Sites upgrading from 19.2.0 or earlier must also follow the 19.3.0 guidance,
including the change custom modules need for form validation.

The `pH7Builder-v19.3.1.zip` asset includes production dependencies. Verify its
attached SHA-256 checksum, then use the
[Quick Start](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.1/docs/QUICK_START.md) and
[Launch Checklist](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.3.1/docs/LAUNCH_CHECKLIST.md).
SMTP delivery, SMS, GeoNames and payments still require checks with your own
provider configuration before going live.

[All changes since 19.3.0](https://github.com/pH7Software/pH7-Social-Dating-CMS/compare/v19.3.0...v19.3.1)
