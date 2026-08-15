# pH7Builder 18.6.0 — Prioritized Backlog

This backlog ranks the known non-release-blocking work by its effect on an
entrepreneur getting from installation to a dependable live service. It does
not weaken the requirements in the [Launch Checklist](LAUNCH_CHECKLIST.md).

## 1. Modernize payment integrations

Keep Stripe and 2Checkout fail-closed until their legacy flows are replaced by
the providers' current APIs. Migrate Braintree away from
[Drop-in](https://developer.paypal.com/braintree/docs/guides/drop-in/overview/javascript/v3)
before its September 1, 2026 deprecation and September 1, 2027 end of support. Add
provider-backed browser tests for successful, declined, cancelled, replayed,
and refunded transactions before enabling a replacement by default.

**Launch impact:** owners cannot safely sell memberships through a gateway
whose bundled flow is unavailable or nearing end of support. PayPal and any
enabled Braintree deployment still require sandbox verification on the real
public callback URL.

## 2. Expand the release environment matrix

Run the packaged browser installer, member signup, admin login, Notes, uploads,
cron, and the direct database migration on every supported PHP minor and the
supported MySQL 8 range. Add Apache and nginx package smoke tests. The Docker
workflow now builds the development image, starts its stack, and verifies the
installer in CI; expand that coverage rather than duplicating it manually.

**Launch impact:** this catches hosting-specific failures before a new owner
meets them during deployment.

## 3. Make media replacement crash-safe

Write replacement avatars and profile backgrounds to a validated temporary
file, verify the final image, atomically rename it, and only then remove the
previous asset. The current path and ownership checks prevent cross-account or
out-of-directory writes, but a filesystem failure during replacement can still
leave the member without the previous image.

**Launch impact:** uncommon storage failures should not turn into visible
profile damage.

## 4. Add durable post-commit work

Move payment notifications, affiliate commissions, and other external effects
that happen after a database commit to an idempotent queue or outbox. Retain
the current transaction and replay protections.

**Launch impact:** a temporary mail or network failure should be recoverable
without repeating a membership or commission change.

## 5. Refresh the administrative first-run experience

Continue replacing ambiguous settings with business-language labels, inline
examples, safe defaults, and a visible launch-progress checklist. Review the
membership, moderation, email, cron, tax, and gateway screens with a new owner
on desktop and mobile before changing behavior.

**Launch impact:** owners make fewer unsafe configuration guesses and reach a
credible first launch sooner.

## 6. Modernize the presentation layer incrementally

Audit the legacy theme and editor dependencies for maintained replacements,
accessibility, mobile behavior, and content migration needs. Replace them in
small, separately testable releases rather than attempting a release-wide UI
rewrite. Compatibility and the exact migration effort are **uncertain** until
each theme and extension is inventoried.

**Launch impact:** a current, accessible first impression improves trust while
small migrations reduce the risk of breaking established custom themes.

## 7. Retire historical internal naming carefully

Public copy uses pH7Builder, with “formerly pH7CMS” retained once for search.
Some internal module names, routes, namespaces, and compatibility identifiers
still contain historical `ph7cms` text. Rename them only with a documented
upgrade path and compatibility tests; do not silently break installed modules
or bookmarked admin routes.

**Launch impact:** low immediate user impact, but a controlled migration keeps
the codebase coherent without disrupting existing sites.

## 8. Automate public release metadata

Make release publication update and verify the external pH7Builder update feed
only after the GitHub tag, ZIP, and checksum are publicly available. For the
18.6.1 patch publication, its `ph7builder` entry must say `REVOLUTIONARY™`,
version `18.6.1`, SQL schema `1.6.6`, and build `1` before `upd-alert` is
enabled.

**Launch impact:** installed sites discover real, downloadable releases without
being sent to missing assets or left on stale version metadata.
