# pH7Builder 20.0.1 — Security Release

Released on 26 November 2026, pH7Builder 20.0.1 updates the locked production
mail dependencies after two upstream Symfony Mailer security advisories.

## Security update

- Updates Symfony Mailer and MIME from 7.4.22 to 7.4.24.
- Closes CVE-2026-133751, which could allow mandatory TLS to be bypassed by a
  network attacker.
- Closes CVE-2026-133755, which could allow a network attacker to inject a reply
  immediately after a STARTTLS command.

## Upgrade notes

There is no database migration from 20.0.0. The SQL schema remains `1.6.7`, and
PHP 8.3 through 8.5 remain supported.

Deploy the complete 20.0.1 package, preserve configuration and user data,
remove or block the installer, and clear application, template, and browser/CDN
caches. Source deployments should run `composer install` from the tagged tree
so Composer installs the patched lock file.
