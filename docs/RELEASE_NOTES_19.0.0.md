# pH7Builder 19.0.0 — Draft Release Notes

> Release candidate only. pH7Builder 19.0.0 has not been published. Stable
> download and installation instructions intentionally remain on 18.6.2 until
> the final v19 package and checksum are public and independently verified.

## Candidate highlights

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
- The current candidate does not change the SQL schema from 18.6.2
  (`1.6.6`). This must be rechecked against the final release commit before
  confirming that no v19 database migration is required.
- Deployments older than 18.6.0 must still apply every applicable documented
  migration in order. Preserve local configuration, uploads, custom modules,
  custom themes, language packs, and payment credentials.
- Stable README, Quick Start, and upgrade commands must not move from 18.6.2
  until a final v19 artifact and checksum exist at those exact URLs.

## Publication gates

- [ ] Confirm the v19 release codename; the candidate currently retains the
      v18 `REVOLUTIONARY™` name rather than inventing a replacement.
- [ ] Set `KERNEL_RELEASE_DATE` to the real publication date.
- [ ] Rebase or merge the final reviewed changes, then rerun PHPUnit, PHPStan,
      syntax checks, Composer validation, and both dependency audits.
- [ ] Build the production ZIP from the exact release commit, verify its
      contents, generate its SHA-256 file, and run a fresh packaged install on
      supported PHP and MySQL versions.
- [ ] Recheck installer token access, schema creation, admin login, public
      signup, member login, Notes, HTML/XML site maps, RSS feeds and feed list,
      responsive public/admin views, logs, and post-install `_install` removal.
- [ ] Update the stable README, Quick Start, upgrade guide, and release notes
      only after the final artifact names and upgrade path are known.
- [ ] Publish the annotated tag, ZIP, and checksum; verify all public asset
      digests before enabling any external update alert.
