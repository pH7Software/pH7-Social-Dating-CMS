# pH7Builder 19.0.0 — Release Notes

Released on 28 August 2026 as **REVOLUTIONARY™**, pH7Builder 19.0.0 is a major
stability, security, PHP 8 compatibility, installer, and usability release for
self-hosted dating and social communities.

## Highlights

- Rejects unsafe template override paths before resolving files, preventing
  path traversal while preserving valid theme overrides.
- Stores only a one-way installer-token hash for the web-server process; the
  one-time plaintext token remains confined to CLI output.
- Removes remaining PHP 8 compatibility failures in HTTP status handling, CSV
  imports, installer checks, website diagnostics, and action-less AJAX routing;
  it also removes obsolete INI reads and the PHP 8.5-deprecated explicit
  FileInfo close call.
- Constrains wall edits and deletions to the requested owner's exact wall post,
  preventing one request from changing multiple posts.
- Validates comment identity and ownership before showing the edit form, gives
  invalid or forbidden requests an appropriate error page, and reports comment
  mutation success only when one row changed.
- Keeps the public splash header readable on small screens and redraws all
  admin charts after viewport changes to prevent horizontal overflow.
- Aligns the base and premium content gutters with responsive grid rows so
  member dashboards no longer extend beyond narrow viewports.
- Emits each RSS discovery link once, escapes third-party video-provider
  errors, and validates remote project-news content before rendering it.
- Renders the HTML Site Map and RSS Feed List from local XML templates instead
  of making blocking HTTP requests back to the same site.
- Falls back to member browsing when IP geolocation is unavailable, preventing
  the People Nearby menu and site-map entry from linking to `/dating//`.
- Keeps the dashboard useful by limiting project news to three concise,
  human-readable items.
- Replaces fresh-site 404 pages in the member, affiliate, subscriber, and mail
  administration lists with clear empty states and useful next actions.
- Adds regression coverage for each corrected security, compatibility, feed,
  and responsive-UI behavior.

## Compatibility and upgrade status

- PHP 8.2 or newer and MySQL 8.0 or newer remain required.
- This release does not change the SQL schema from 18.6.2 (`1.6.6`), so there
  is no `18.6.2-19.0.0` database migration.
- Deployments older than 18.6.0 must still apply every applicable documented
  migration in order. Preserve local configuration, uploads, custom modules,
  custom themes, language packs, and payment credentials.

## Upgrade

Back up the database, application files, local configuration, uploads, custom
modules and themes, language packs, and gateway credentials. Upgrade a staging
copy first and follow the complete [Upgrade Guide](UPGRADING.md). Deployments
older than 18.6.0 must apply each applicable documented database migration in
order.

The ready-to-run `pH7Builder-v19.0.0.zip` archive includes locked production
dependencies. Verify it with the attached `.sha256` file before deployment,
then follow the [Production Quick Start](QUICK_START.md) and
[Launch Checklist](LAUNCH_CHECKLIST.md).
