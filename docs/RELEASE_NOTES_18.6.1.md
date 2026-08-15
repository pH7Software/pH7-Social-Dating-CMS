# pH7Builder 18.6.1 — Release Notes

## Highlights

- Preserves signup recovery parameters across immediate and delayed redirects.
  HTML output remains encoded while HTTP headers use a validated request target.
- Requires members to choose their identity and matching preferences instead
  of guessing them from a first name, and avoids silently selecting a country
  when geolocation has no reliable result.
- Makes the three-step signup journey clearer and more usable on desktop and
  mobile, with better progress, guidance, and validation recovery.
- Restores the optional homepage video splash beneath readable login and signup
  content.
- Reports exact installer completion, clarifies database and URL-rewriting
  guidance, and aligns installer attribution with the project license.
- Restores useful project history, product imagery, creator information, and
  legacy context while keeping the README focused on a fast first launch.

## Upgrade notes

- This is a code-only patch over 18.6.0. The SQL schema remains `1.6.6`; there
  is no `18.6.0-18.6.1` database migration.
- Deploy the 18.6.1 package without overwriting local configuration, uploaded
  data, custom modules, custom themes, or payment credentials.
- Source deployments must reinstall the locked production dependencies.
- Clear application caches, then complete a new signup through login and check
  the homepage, admin dashboard, Notes, email, cron, and enabled payments.
- Sites older than 18.6.0 must follow every applicable intermediate upgrade
  path, including the 18.5.1-18.6.0 database migration.

PHP 8.2+ and MySQL 8.0+ remain required. Older MySQL versions, MariaDB, and
PostgreSQL are not verified or supported by this patch.

## Release verification before publication

- [x] Framework and installer versions, release date, docs, planned tag, and
      release title all identify `18.6.1`.
- [x] The complete PHPUnit suite, PHPStan, PHP syntax sweep, Composer
      validation, and dependency audits pass.
- [x] A fresh MySQL 8 install completes, including admin creation and installer
      removal.
- [x] Signup completes through login; Notes, admin, video splash, and responsive
      layouts remain functional.
- [x] A reproducible release-candidate ZIP is built from the committed tree,
      includes locked dependencies, excludes development files, and matches its
      SHA-256 file.

## Post-publication operational step

- [ ] After the GitHub tag, ZIP, and checksum are public, update the live
      `ph7builder` feed entry to `REVOLUTIONARY™`, version `18.6.1`, SQL schema
      `1.6.6`, and build `1`; enable `upd-alert` only after its download target
      is verified.
