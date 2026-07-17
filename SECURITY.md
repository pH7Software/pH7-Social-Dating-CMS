# Security Policy

## Supported versions

Only the latest stable release and the current development branch receive security fixes. Please keep your installation up to date.

## Reporting a vulnerability

Please do **not** report security vulnerabilities through public GitHub issues, discussions, or pull requests.

Instead, use one of these private channels:

1. **GitHub private vulnerability reporting** (preferred): [Report a vulnerability](https://github.com/pH7Software/pH7-Social-Dating-CMS/security/advisories/new)
2. **Email**: hi@ph7.me — include "SECURITY" in the subject line.

Please include: the affected version/branch, steps to reproduce (or a proof of concept), and the impact you believe it has. You will receive an acknowledgement within a few days. Please allow a reasonable disclosure window for a fix to be released before any public disclosure — coordinated disclosure is always honored with credit in the release notes (unless you prefer to stay anonymous).

## Scope notes

- pH7Builder is self-hosted software; server/hosting misconfiguration is out of scope, but insecure *defaults* shipped by pH7Builder are firmly in scope.
- Dependency vulnerabilities are monitored via `composer audit` in CI; still, reports about vulnerable bundled assets (JS libraries under `static/`) are very welcome.
