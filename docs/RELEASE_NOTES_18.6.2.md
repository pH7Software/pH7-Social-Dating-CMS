# pH7Builder 18.6.2 — Release Notes

## Highlights

- Restricts gettext plural rules from language packs to the expected numeric
  grammar instead of permitting unexpected identifiers.
- Falls back to a safe English singular/plural rule when a language pack has a
  malformed expression, avoiding a broken request while preserving valid
  gettext formulas.
- Adds regression coverage for rejected identifiers and invalid arithmetic.

## Upgrade notes

- This is a code-only patch over 18.6.1. The SQL schema remains `1.6.6`; there
  is no `18.6.1-18.6.2` database migration.
- Deploy the 18.6.2 package without overwriting local configuration, uploaded
  data, custom modules, custom themes, language packs, or payment credentials.
- Source deployments must reinstall the locked production dependencies.
- Clear application caches, then verify every installed language and its plural
  strings before reopening the site.
- Sites older than 18.6.0 must follow every applicable intermediate upgrade
  path, including the 18.5.1-18.6.0 database migration.

PHP 8.2+ and MySQL 8.0+ remain required. Older MySQL versions, MariaDB, and
PostgreSQL are not verified or supported by this patch.

## Release verification before publication

- [ ] Framework and installer versions, release date, docs, planned tag, and
      release title all identify `18.6.2`.
- [ ] The complete PHPUnit suite, PHPStan, PHP syntax sweep, Composer
      validation, and dependency audits pass.
- [ ] A fresh MySQL 8 install completes, including admin creation and installer
      removal.
- [ ] Valid gettext plural formulas work and malformed expressions fail safely.
- [ ] A reproducible release-candidate ZIP is built from the committed tree,
      includes locked dependencies, excludes development files, and matches its
      SHA-256 file.

## Post-publication operational step

- [ ] After the GitHub tag, ZIP, and checksum are public, update the live
      `ph7builder` feed entry to `REVOLUTIONARY™`, version `18.6.2`, SQL schema
      `1.6.6`, and build `1`; enable `upd-alert` only after its download target
      is verified.
