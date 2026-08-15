# pH7Builder Launch Checklist

Use this checklist on the real production domain before accepting members or
payments. A successful installer run is the beginning of launch preparation,
not the end.

## Hosting and security

- [ ] The server runs supported PHP and MySQL versions, with production error
      display disabled and security updates enabled.
- [ ] HTTPS is valid on every public and admin URL; HTTP redirects to HTTPS.
- [ ] `_install` is removed.
- [ ] `_protected`, `_repository`, `_tests`, `_tools`, `docker`, `.git`,
      configuration, logs, database dumps, and source templates cannot be
      downloaded over HTTP.
- [ ] Only the required runtime/configuration directories are writable by the
      web-server account; nothing is world-writable.
- [ ] The database uses a unique least-privilege account, and no credentials or
      real user data are committed to Git.
- [ ] The admin password is unique and strong; additional admin accounts,
      admin IP restrictions, and two-factor authentication have been reviewed.
- [ ] Session cookies are inspected over HTTPS and the reverse proxy forwards
      the original protocol safely.
- [ ] File-upload size, image/video types, storage limits, and moderation
      behavior have been tested with valid and invalid files.
- [ ] A private vulnerability-reporting contact is monitored.

## Brand and first impression

- [ ] Site name, logo, favicon, colors, homepage headline, metadata, and default
      images match the business.
- [ ] Placeholder copy, sample accounts, generated profiles, test media, and
      test payments are removed or clearly isolated.
- [ ] Mobile and desktop pages have been checked while signed out, signed in,
      and signed in as an administrator.
- [ ] Registration, login, password reset, member search, messaging, report,
      block, account deletion, and logout all complete without an error page.
- [ ] Disabled or unconfigured modules do not appear in navigation.

## Legal and privacy

- [ ] Terms, Privacy, Cookie, Acceptable Use, Safety, Refund, and Contact pages
      identify the actual operator and jurisdiction.
- [ ] The privacy notice explains collected profile data, location, media,
      cookies, analytics, payments, retention, processors, and user rights.
- [ ] Consent and age/eligibility flows match the launch countries and niche.
- [ ] GDPR/UK GDPR or other applicable access, correction, export, objection,
      and deletion requests have an owned process and response contact.
- [ ] Cookie consent and analytics behavior have been tested before consent
      where local law requires it.
- [ ] Copyright/reporting, law-enforcement, and emergency escalation processes
      are documented for the operating team.

This is an operational checklist, not legal advice. Have qualified counsel
review obligations for the countries, age groups, content, and payment model
you serve.

## Trust, moderation, and anti-abuse

- [ ] Member activation, profile approval, photo moderation, report queues, and
      moderator permissions are configured and tested.
- [ ] At least two people know how to respond to harassment, impersonation,
      scams, minors, non-consensual imagery, and credible safety threats.
- [ ] Registration throttling, CAPTCHA, blocked usernames/emails/IPs/countries,
      duplicate-content checks, and login protection use intentional values.
- [ ] Fake/generated profiles are disabled or clearly disclosed and never
      presented as real people.
- [ ] Reports and moderation queues have owners, coverage hours, response
      targets, evidence retention, and an appeal route.
- [ ] A test moderator can suspend an account, remove media, preserve evidence,
      and restore a mistaken action.

## Email and deliverability

- [ ] SMTP is configured as `PH7_MAILER_DSN` in the PHP web-process environment,
      or the server/hosting sendmail relay works. No credential is stored in
      source control.
- [ ] pH7Builder's Email settings contain the correct sender, admin, feedback,
      and return address.
- [ ] SPF, DKIM, and DMARC pass for the production domain.
- [ ] Registration activation, password reset, moderation, billing, and admin
      notifications arrive at major mailbox providers and render correctly.
- [ ] Bounces and abuse complaints go to monitored mailboxes.
- [ ] Unsubscribe behavior is tested for promotional/newsletter messages.

## Cron, backups, and operations

- [ ] All four documented cron URLs use HTTPS, a unique secret, and a monitored
      scheduler; the secret is not in source control.
- [ ] Birthday, blocked-IP, database-maintenance, and general jobs have each
      completed successfully at least once.
- [ ] Automated database, uploaded-media, and configuration backups are stored
      off-server, encrypted where appropriate, and retained by policy.
- [ ] A restore has been completed on a separate environment and its time was
      recorded.
- [ ] Disk, database, PHP-FPM, web-server, queue/cron, certificate, backup, and
      application-error monitoring alert a real person.
- [ ] A rollback plan and maintenance message are ready for the first release.

## Memberships and payments

- [ ] Every membership name, permission, price, currency, duration, renewal,
      cancellation, and refund statement matches what the checkout promises.
- [ ] Unused gateways are disabled and no example keys remain.
- [ ] Stripe stays disabled in 18.6.x; the bundled legacy flow must be migrated
      to Checkout Sessions or Payment Intents before it is offered to members.
- [ ] 2Checkout stays disabled until its bundled legacy flow is migrated to the
      2Checkout API 6.0.
- [ ] If Braintree is enabled, its Drop-in UI migration is scheduled before
      the provider's September 1, 2026 deprecation and September 1, 2027 end of
      support.
- [ ] Sandbox/test mode covers success, decline, cancellation, callback/webhook,
      duplicate notification, refund, and expired membership behavior.
- [ ] The PayPal IPN URL is reachable over public HTTPS and a repeated verified
      notification leaves one completed transaction and one membership update.
- [ ] Live keys are stored only in production and the intended gateway is
      explicitly switched to live mode.
- [ ] A low-value live purchase and refund have been reconciled against the
      gateway dashboard and the member's access.
- [ ] Tax, invoice/receipt, chargeback, and support responsibilities have named
      owners.

## Final go-live test

- [ ] Create a brand-new member in a private browser using a real external
      mailbox.
- [ ] Activate the account, complete the profile, upload media, search, send a
      message, block/report another test account, and reset the password.
- [ ] Complete the expected free or paid membership journey.
- [ ] Delete the account and verify the documented retention/deletion outcome.
- [ ] Review browser console/network errors, application logs, email delivery,
      cron output, payment events, mobile layout, and accessibility basics.
- [ ] Remove all remaining test data, take a final backup, and record the
      deployed Git tag and rollback point.
