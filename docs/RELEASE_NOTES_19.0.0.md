# pH7Builder 19.0.0 — Draft Release Notes

> Release candidate only. pH7Builder 19.0.0 has not been published. Stable
> download and installation instructions intentionally remain on 18.6.2 until
> the final v19 package and checksum are public and independently verified.

## Candidate highlights

- Rejects unsafe template override paths before resolving files, preventing
  path traversal while preserving valid theme overrides.
- Makes the out-of-band installer token readable by the web-server process
  without exposing it to other users on the host.
- Removes remaining PHP 8 compatibility failures in HTTP status handling, CSV
  imports, installer checks, and website diagnostics.
- Keeps the public splash header readable on small screens and redraws all
  admin charts after viewport changes to prevent horizontal overflow.
- Emits each RSS discovery link once and escapes third-party video-provider
  errors before rendering them.
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
      signup, member login, Notes, RSS, responsive public/admin views, logs,
      and post-install `_install` removal.
- [ ] Update the stable README, Quick Start, upgrade guide, and release notes
      only after the final artifact names and upgrade path are known.
- [ ] Publish the annotated tag, ZIP, and checksum; verify all public asset
      digests before enabling any external update alert.
