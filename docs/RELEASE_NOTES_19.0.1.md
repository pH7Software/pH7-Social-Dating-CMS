# pH7Builder 19.0.1 — Release Notes

Released on 5 September 2026, this patch makes everyday forms clearer and fixes
interactions with the bundled jQuery UI. It also includes the maintenance
updates merged since 19.0.0.

## Highlights

- Improves base and premium form readability, touch targets, dark-mode colours,
  error messages, and password-toggle layout.
- Keeps agreement checkboxes from disabling buttons in unrelated forms.
- Restores PFBC Ajax submission and lets users retry failed requests.
- Renders age fields as native sliders with independent live counters, and
  updates textarea character counts when text is pasted.
- Restores recipient avatars and keyboard selection, fits suggestions to narrow
  screens, and keeps results after the signed-in user's own profile is skipped.
- Uses the selected city's state and postcode, respects the current country,
  and clears autocomplete loading indicators after request failures.
- Updates locked Symfony Console and Mailer patch versions, clarifies
  shared-host SMTP setup and payment-provider limitations, and expands
  regression coverage and isolated browser previews for contributors.

## Compatibility and upgrade

No intentional breaking changes. PHP 8.2+ and MySQL 8.0+ remain required, and
the SQL schema stays at `1.6.6`. No database migration is needed from 19.0.0.
Bootstrap, jQuery, and jQuery UI versions are unchanged in this patch.

Back up first and test a staging copy. Preserve configuration, uploads, custom
modules/themes, language packs, and credentials. Deploy PHP and browser assets
together, install locked dependencies when deploying from source, remove the
installer on existing sites, and clear application, CDN, and browser caches.
Custom form overrides may need to incorporate the updated styles and markup.
Follow the [Upgrade Guide](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.0.1/docs/UPGRADING.md).

The `pH7Builder-v19.0.1.zip` asset includes production dependencies. Verify it
with the attached `.sha256` file, then follow the
[Quick Start](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.0.1/docs/QUICK_START.md) and
[Launch Checklist](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/v19.0.1/docs/LAUNCH_CHECKLIST.md).
SMTP delivery, GeoNames availability, and payment processing must be tested
with your own provider configuration before going live.

[All changes since 19.0.0](https://github.com/pH7Software/pH7-Social-Dating-CMS/compare/v19.0.0...v19.0.1)
