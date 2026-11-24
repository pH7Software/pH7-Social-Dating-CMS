---
name: ph7-i18n-audit
description: Audit pH7Builder internationalisation changes, Gettext PO/POT catalogues, locale bundles, PH7Tpl translations, placeholders, plural forms, locale fallback, and language-aware routes. Use when user-facing text, t()/nt(), {lang ...}, JavaScript copy, locale resolution, or pH7-Internationalization content changes.
---

# pH7Builder i18n audit

Work from the pH7Builder repository root.
The audit requires PHP 8.3 or newer. GNU Gettext `msgfmt` is optional and adds
its native catalogue validation when available.

1. Limit the review to changed PHP, PH7Tpl, JavaScript, routes, and locale
   assets. Identify user-facing literals that bypass the existing `t()`,
   `nt()`, or `{lang ...}` paths. Do not flag protocol values, identifiers,
   logs, developer errors, or third-party/minified assets as translations.
2. Run the deterministic catalogue and bundle audit:

   ```console
   php .agents/skills/ph7-i18n-audit/scripts/audit.php
   ```

   The command also audits an adjacent `pH7-Internationalization` checkout
   when present. Use `--core-only` when it is unavailable, or
   `--translations=/absolute/path` to select another checkout.
3. Preserve every placeholder exactly, including `%0%`, `%n%`, and
   `%site_name%`. Verify singular and plural outcomes, not only catalogue
   syntax.
4. For locale-selection or fallback changes, run the closest
   `Framework/Translate` tests plus the relevant router tests. Exercise an
   unsupported locale, a supported locale, Unicode text, and an RTL locale when
   those behaviours are affected.
5. For route changes, keep route generation through `Uri::get()`. Verify the
   translated route file and the fallback to the default route file.
6. Report catalogue defects separately from missing human translations. Never
   invent, mass-rewrite, or publish translations. The language-pack repository
   is GPL-licensed; do not copy its content into the MIT core without an
   explicit licence decision.

Keep regression tests behavioural. Do not add source-string assertions merely
to prove that a translation call exists.
