# pH7Builder 19.4.0 — Release Notes

Release date: 10 November 2026.

This security, maintenance, and accessibility release includes the following
updates since 19.3.3.

## Highlights

- Restricts mail search results to messages belonging to the signed-in member
  and applies the mail-read permission to search results.
- Accepts only valid string values in scalar form validators while preserving
  support for missing optional fields.
- Trusts forwarded client IP headers only when explicitly enabled and the
  direct peer matches a configured proxy. Configure
  `PH7_TRUSTED_PROXIES` with exact comma-separated IP addresses (CIDR ranges
  are not supported), and ensure trusted proxies replace client-supplied
  forwarding headers.
- Updates Symfony Filesystem from 7.4.18 to 7.4.23 to prevent filesystem path
  escapes.
- Improves keyboard and screen-reader access with semantic mail-message links
  and accessible labels for bulk-selection checkboxes.

## Compatibility and upgrade

PHP 8.2+, MySQL 8.0+ and schema `1.6.6` are unchanged. No database migration is
needed from 19.3.3. Deployments behind a reverse proxy must set
`PH7_TRUST_PROXY_HEADERS=1` and configure `PH7_TRUSTED_PROXIES` for the actual
proxy addresses. Direct nginx/Apache deployments normally need neither proxy
variable.

Deploy the complete package, preserve configuration and user data, remove the
installer on existing sites, and clear application, template and CDN caches.
See the [Upgrade Guide](UPGRADING.md) and [Production Quick Start](QUICK_START.md).

The release archive should include production dependencies and an attached
SHA-256 checksum.
