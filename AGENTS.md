# pH7Builder Agent Guide

## Scope and sources of truth

This file is the canonical project-wide instruction source. A more specific
`AGENTS.md` may override it only inside its directory.

Before editing, read the relevant nearby code and [`CONTRIBUTING.md`](CONTRIBUTING.md).
Treat the implementation, `.editorconfig`, `.php-cs-fixer.dist.php`,
`phpcs.xml.dist`, `phpstan.neon.dist`, and `phpunit.xml.dist` as executable
sources of truth. Preserve unrelated work and avoid loading unrelated parts of
the repository.

pH7Builder 20.0.0 requires PHP 8.3 or newer. It is a modular MVC application
built on the project-specific pH7Framework and PH7Tpl template engine.

## Task-specific workflow

- Use `$ph7builder-installation` for a local evaluation, published release
  installation, or production deployment preparation.
- Use `$ph7builder-module-development` when creating or substantially changing
  a module.
- Use `$ph7builder-upgrade-preflight` to assess an installation upgrade or
  design upgrade tooling.
- Use `$ph7-i18n-audit` when changing user-facing strings, locale selection,
  Gettext catalogues, PH7Tpl translation calls, language-aware routes, or a
  language pack. Do not generate or publish human translations with AI.
- Start with the narrowest deterministic check that covers the change. Expand
  to the CI suite, static analysis, browser audit, or integration environment
  only when the affected boundary requires it.
- Do not rerun unchanged checks. Record commands and their actual exit status;
  never report a skipped or failing check as passed.

## Architecture and repository map

- `_protected/app/system/modules/`: bundled modules, normally split into
  controllers, models, forms, views, config, language files, and assets.
- `_protected/app/modules/`: optional/custom modules; `helloworld` is the
  minimal structural example.
- `_protected/app/system/core/` and `_protected/app/includes/`: shared
  application behaviour, base classes, helpers, and the app autoloader.
- `_protected/framework/`: generic pH7Framework infrastructure with its own
  autoloader and filename suffixes.
- `_protected/app/configs/routes/`: language-aware XML routes.
- `_protected/app/system/modules/*/views/<theme>/tpl/`: module PH7Tpl views.
- `templates/themes/`: global layouts and public theme assets.
- `templates/system/modules/`: system-module theme assets and configuration.
- `static/`: shared browser assets and vendored browser libraries.
- `_install/`: installer application and canonical fresh-install schema.
- `_repository/upgrade/`: versioned upgrade migrations.
- `_tests/Unit/`: PHPUnit tests mirroring source areas where practical.
- `_tools/`: project maintenance, preview, packaging, and audit tools.
- `data/` and `_protected/data/`: runtime/user data. Commit only intentional
  defaults or placeholders, never generated content.

Keep HTTP orchestration in controllers, persistence in models, reusable app
behaviour in `system/core` or `includes`, and generic infrastructure in the
framework. Generate application URLs with `Uri::get()` and respect XML routing.

Treat these boundaries as a revisable default, not an obstacle. When a concrete
requirement fits poorly, compare a direct patch with the smallest improvement
to the responsible contract or seam. Choose the solution with the lowest total
complexity across callers, migrations, tests, operations, and obsolete code.

Avoid parallel flows, speculative abstractions, unnecessary dependencies,
compatibility layers without a demonstrated caller, and unrelated rewrites.
Complete justified architectural changes coherently, then remove the verified
superseded path. Prefer the smallest targeted edit and preserve unrelated code,
formatting, comments, documentation, and behaviour. If a change replaces a
substantial part of a file, first reconsider the owning boundary or a focused
refactor; larger refactors remain valid when they clearly reduce total
complexity and preserve required behaviour.

## Coding conventions

Follow nearby code where legacy compatibility constrains a change. For new or
substantially rewritten code:

- Use `UpperCamelCase` for classes, interfaces, and traits; prefer natural
  adjective interface names ending in `-able` or `-ible`.
- Use `camelCase` methods with descriptive verbs. Use `is`, `has`, `can`, or
  `should` for predicates when accurate. Constants use
  `ALL_CAPS_WITH_UNDERSCORES`.
- Use the least visibility that supports the design. Avoid public mutable
  state. Add native types where accurate and inheritance-compatible.
- Keep the project variable type prefixes accurate: `a` array, `i` integer,
  `f` float, `b` boolean, `c` character, `s` string, `by` byte, `r` resource,
  `o` object, and `m` mixed.
- Use `lowercase_with_underscores` for new global functions, global/session/
  cookie keys, and project-owned array keys. Preserve names imposed by forms,
  templates, configuration, database columns, and external APIs.
- Prefer focused, cohesive functions, guard clauses, and local explicit
  mutation. Extract a complex condition or pure operation only when the name
  clarifies intent. Replace unexplained business values with named constants.
- Reuse existing framework helpers and components. Avoid unnecessary state,
  indirection, duplicate implementations, dead fallbacks, and mechanical
  transformations. Remove dead code only when its callers are understood.
- Comments explain non-obvious decisions, constraints, and invariants, not code
  that is already clear. Follow surrounding PHPDoc and document public APIs or
  non-obvious contracts.
- Use short array syntax, strict comparisons, and the format produced by the
  repository's PHP CS Fixer. Do not impose arbitrary function-size or style
  limits beyond the project tooling.

In `_protected/framework`, class, trait, and interface files end in
`.class.php`, `.trait.php`, and `.interface.php`; the filename and symbol must
match. Application classes under `_protected/app` normally use plain `.php`.
Do not rename framework files to ordinary `.php`; its autoloader depends on the
suffixes. PHPUnit test classes end in `Test.php`.

### Database, templates, and localisation

- New tables use lowercase underscore names beginning with `ph7_`; installation
  replaces that prefix. Use `Db::prefix()` and existing `DbTableName` constants.
- Bind untrusted SQL values. Validate identifiers that cannot be bound.
- Every schema change must cover both a fresh install and the supported upgrade
  path. Update the installer schema and add a new versioned migration; never
  rewrite historic migrations.
- Preserve PH7Tpl syntax and the controller/action view layout. Keep global
  theme changes in `templates/themes/<theme>/` and module changes in the
  module's view tree. Inspect every affected base/premium variant deliberately.
- Keep shared assets in `static/` and module-theme assets under
  `templates/system/modules/<module>/themes/<theme>/`.
- Use `t()`/`nt()` in PHP and the existing `{lang ...}` PH7Tpl syntax. Preserve
  placeholder names, plural forms, Unicode, locale fallback, and language-aware
  route behaviour.
- The separate `pH7Software/pH7-Internationalization` repository contains
  language packs under a different licence. Validate it in place; do not copy
  or relicense its content without explicit licence compatibility.

## Security invariants

Security controls are layered; do not remove one because another exists.

- Authentication/authorization and CSRF are separate. Module
  `config/Permission.php` files establish access; state-changing form actions
  must also validate their token. For `LinkCoreForm` and `ConfirmCoreForm`, use
  `requireActionToken()`/`requireCurrentUrlToken()` or an equivalent verified
  server-side check.
- Treat request values, stored rows, third-party data, and file metadata as
  untrusted at each output or mutation boundary.
- Encode for the final context: `escape()` for HTML text and
  `Str::escapeAttribute()` for attributes. Sanitize intentional rich HTML with
  the maintained `Purifier`/`Filter::xssClean()` path.
- Never build filesystem paths directly from request data. Validate basenames,
  IDs, extensions, and containment inside the intended base directory.
- Preserve ownership checks when mutating user resources; authentication alone
  does not prove ownership of a posted record ID.
- Make security failures explicit. Do not weaken settings or add silent
  fallbacks merely to make a request succeed.
- For a vulnerability fix, search equivalent sources and sinks across modules
  and themes, but retain only evidence-based patches. Do not duplicate an
  already-correct sanitizer, token check, or path transformation.
- Never commit credentials, local configuration, real user data, logs, backups,
  caches, generated templates, or private vulnerability details.

## Dependencies and generated files

- `composer install` installs root dependencies in `_protected/vendor/` and
  invokes the installer dependency setup. Never patch either vendor directory.
- Commit root `composer.lock` with root dependency changes and
  `_install/composer.lock` with installer dependency changes.
- Composer install/update copies Bootstrap distributions into `static/`; keep
  those diffs only when the dependency update intentionally changes them.
- Respect `.gitignore`. Inspect every untracked file before staging and exclude
  machine-local or generated noise.

## Verification

Test observable outcomes and contracts, not source spelling or layout. A useful
regression test fails for a user-visible, caller-visible, provider, security, or
release-artifact defect and survives behaviour-preserving refactors. Use unit
tests for isolated logic/provider boundaries, integration tests for
collaborating components, and `_tools/browser-audit.sh` for DOM, JavaScript,
keyboard, form, responsive, and browser behaviour. Keep static contract tests
only when the repository artifact itself is the contract and execution is
impractical. Add the smallest non-duplicate regression test.

```console
# Changed PHP files
php -l path/to/file.php
_protected/vendor/bin/php-cs-fixer fix --path-mode=intersection <changed-files>

# Focused, deterministic, then broad verification
_protected/vendor/bin/phpunit path/to/Test.php --no-coverage
composer test
composer analyse

# Dependencies
composer validate --strict
composer audit --locked
composer --working-dir=_install validate --strict
composer --working-dir=_install audit --locked

# Repository hygiene
git diff --check
```

Use `composer test-integration` only with its required installed application and
external services. For release or significant browser-facing changes, run the
Chrome audit. For CSS, also inspect `_tools/theme-preview.html` in light and
dark presentation.

## Git and public-repository hygiene

- Do not discard, overwrite, stage, or commit unrelated user changes.
- Keep each commit to one purpose. A subject needing “and” or “+” usually needs
  separate commits.
- Use short, present-tense subjects that state the change and its reason.
- Review the complete staged diff. Public commits exclude secrets, private
  audit material, runtime/user data, caches, and generated noise.
