# pH7Builder Agent Guide

## Scope and sources of truth

This file applies to the entire repository. More specific `AGENTS.md` files, if
added later, override it only within their directories.

Before changing code, read the relevant nearby files and
[`CONTRIBUTING.md`](CONTRIBUTING.md). Treat the current implementation,
`.editorconfig`, `.php-cs-fixer.dist.php`, `phpcs.xml.dist`,
`phpstan.neon.dist`, and `phpunit.xml.dist` as the executable sources of truth.
Keep changes focused and preserve unrelated work already present in the
worktree.

pH7Builder targets PHP 8.2 or newer and combines a modular MVC application with
the project-specific pH7Framework and PH7Tpl template engine.

## Repository map

- `_protected/app/system/modules/`: bundled application modules. A module
  commonly contains `controllers/`, `models/`, `forms/`,
  `forms/processing/`, `views/`, `config/`, `lang/`, `inc/`, and `assets/`.
- `_protected/app/modules/`: optional/custom modules. Use the `helloworld`
  module as the minimal structural example.
- `_protected/app/system/core/`: shared application controllers, models,
  forms, classes, and assets used across modules.
- `_protected/app/includes/`: application base classes, helpers, and the
  application autoloader.
- `_protected/app/configs/routes/`: language-aware XML routes. Module routing
  may select either `system/modules` or `modules`.
- `_protected/framework/`: pH7Framework. It uses its own autoloader and filename
  suffixes rather than Composer PSR-4 for most framework classes.
- `_protected/app/system/modules/*/views/<theme>/tpl/`: module PH7Tpl
  templates.
- `templates/themes/`: global page layouts and public theme assets.
- `templates/system/modules/`: public CSS, JavaScript, images, and configuration
  belonging to system-module themes.
- `static/`: shared browser assets and vendored browser libraries.
- `data/`: public runtime/user media. Most runtime content is ignored; commit
  only intentional default assets or placeholder files.
- `_protected/data/`: private cache, logs, backups, temporary files, fonts, and
  bundled backgrounds. Do not commit generated runtime files.
- `_install/`: installer application and the canonical new-install SQL schemas.
- `_repository/upgrade/`: versioned upgrade migrations. Do not rewrite historic
  migrations; add a migration for the intended upgrade path.
- `_tests/Unit/`: PHPUnit tests. Test namespaces and directories should mirror
  the source area as closely as the existing suite permits.
- `_tools/`: project maintenance, CLI, and local preview tools.

Put a change in the narrowest owning layer. Keep HTTP orchestration in
controllers, persistence in models, reusable application behavior in
`system/core` or `includes`, and generic infrastructure in the framework.
Generate application URLs with `Uri::get()` and respect the XML routing model
instead of hardcoding route strings.

## Coding conventions

Follow the surrounding file when legacy compatibility constrains a change, but
use the conventions below for new or substantially rewritten code.

### Classes, interfaces, traits, methods, and constants

- Use `UpperCamelCase` alphanumeric names for classes, interfaces, and traits.
- Prefer adjective interface names ending in `-able` or `-ible` when natural:
  `Controllable`, `Configurable`, `Hashable`, `Readable`, `Serializable`.
- Use `camelCase` alphanumeric method names.
- Use `ALL_CAPS_WITH_UNDERSCORES` for constants.
- Use the least visibility that supports the design. Public controller actions
  and public APIs are expected; avoid public mutable properties.
- Add parameter, property, and return types where they are accurate and
  compatible with the inheritance hierarchy. The project naming convention
  complements native PHP types; it does not replace them.
- Do not introduce compatibility aliases, duplicate implementations, or dead
  fallback code without a demonstrated caller.

Example:

```php
final class MyClass
{
    private const EXPECTED_VALUE = 'abcd';

    public function normalizeValue(string $sValue): string
    {
        return $sValue === self::EXPECTED_VALUE
            ? self::EXPECTED_VALUE
            : 'zyxw';
    }
}
```

### Framework and application filenames

- In `_protected/framework`, classes end in `.class.php`, traits in
  `.trait.php`, and interfaces in `.interface.php`. The filename and declared
  symbol must match exactly.
- Application classes under `_protected/app` normally use plain `.php`
  filenames, following the module's established structure.
- PHPUnit test classes end in `Test.php`.
- Do not rename framework files to ordinary `.php`; the framework autoloader
  explicitly resolves the project suffixes.

### Variables and data names

Variables use camelCase with a leading type indicator. Keep the prefix accurate
when a value changes type:

- `a`: array
- `i`: integer
- `f`: float/double
- `b`: boolean
- `c`: one character
- `s`: string
- `by`: byte, in legacy/low-level contexts
- `r`: resource
- `o`: object
- `m`: mixed

Examples include `$aUsers`, `$iProfileId`, `$fPrice`, `$bEnabled`, `$sUsername`,
`$oUser`, and `$mValue`.

Use `lowercase_with_underscores` for new global functions, global/session/cookie
keys, and project-owned array keys. Preserve exact names required by existing
forms, templates, configuration, database columns, or external APIs. Declare
arrays with short syntax:

```php
$aValues = [
    'my_key' => 'Value',
    'my_key2' => 'Value 2'
];
```

Use four spaces, UTF-8, LF endings, and a final newline. YAML and JSON use two
spaces. There is no hard 80-column limit, but split expressions when doing so
improves comprehension. Prefer strict comparisons, early returns, and the
format produced by the repository's PHP CS Fixer configuration.

### Database conventions

- New table names are lowercase words separated by underscores and begin with
  `ph7_`; installation replaces this default with the configured prefix.
- Use `Db::prefix()` and existing `DbTableName` constants in application code.
- Bind untrusted values through prepared statements. Never concatenate request
  data into SQL.
- Schema changes must support both fresh installations and upgrades. Update the
  applicable installer schema and add the appropriate versioned migration once
  the target release path is known.

### Templates and browser assets

- PH7Tpl files use `.tpl`. Preserve PH7Tpl syntax and the controller/action
  directory convention already used by the module.
- Keep global theme changes in `templates/themes/<theme>/` and module-specific
  view changes in the module's `views/<theme>/` tree.
- If behavior exists in base and premium variants, inspect both and update every
  affected variant deliberately.
- Keep shared assets in `static/`; keep module-theme assets in
  `templates/system/modules/<module>/themes/<theme>/`.
- For CSS changes, use `_tools/theme-preview.html` when it covers the affected
  theme and check both light and dark presentation.

## Security invariants

Security controls are layered and must not be removed merely because another
layer exists.

- Authentication/authorization and CSRF protection are different checks.
  Module `config/Permission.php` files establish access; state-changing form
  actions must also validate the token generated by the form. For
  `LinkCoreForm` and `ConfirmCoreForm` actions, use the shared
  `requireActionToken()`/`requireCurrentUrlToken()` pattern or an equivalent
  verified server-side check.
- Treat every request value, database value, third-party API value, and
  previously stored row as untrusted at its output boundary.
- Encode plain text for its final context. Use `escape()` for HTML text and
  `Str::escapeAttribute()` for HTML attributes. Do not substitute one context
  for another.
- When rich HTML is intentionally supported, sanitize it with the maintained
  `Purifier`/`Filter::xssClean()` path and still apply the appropriate output
  policy where the value is consumed.
- Use prepared SQL statements and validate identifiers that cannot be bound.
- Never construct filesystem paths directly from request data. Validate
  basenames, numeric IDs, extensions, and containment within the intended base
  directory before reading, writing, or deleting.
- Preserve ownership checks when mutating user resources. An authenticated
  session alone does not prove ownership of a posted record ID.
- Do not commit credentials, local configuration, real user data, logs,
  backups, caches, generated templates, or private vulnerability details.
- Avoid weakening security settings or adding a silent fallback to make a
  failing request succeed. Make security failures explicit and test them.

When fixing a vulnerability, search for equivalent sources and sinks across
modules and themes, but keep each retained patch evidence-based. Do not add a
second sanitizer, token check, or path transformation when the same boundary is
already correctly protected.

## Dependencies and generated files

- Install root dependencies with `composer install`; Composer places them in
  `_protected/vendor/` and also installs `_install` dependencies.
- Never patch files under `_protected/vendor/` or `_install/vendor/`.
- Commit root `composer.lock` when `composer.json` dependencies change.
  `_install/composer.lock` is intentionally ignored.
- Composer install/update copies Bootstrap distribution assets into `static/`.
  Review those generated diffs and retain them only when the dependency change
  intentionally updates the public assets.
- Respect `.gitignore`. Before staging, inspect every untracked file and omit
  machine-local configuration, runtime data, test caches, coverage, and logs.

## Verification

Run checks proportionate to the change. Start focused and expand:

```console
# Syntax-check each changed PHP file
php -l path/to/file.php

# Run one test file or directory
_protected/vendor/bin/phpunit path/to/Test.php --no-coverage

# Deterministic unit/CI suite
_protected/vendor/bin/phpunit --testsuite "pH7Builder Protected CI" --no-coverage

# Full configured suite when its external requirements are available
composer test

# Static analysis
composer analyse

# Format changed source files under _protected
_protected/vendor/bin/php-cs-fixer fix --path-mode=intersection <changed-files>

# Repository hygiene
git diff --check
```

For dependency changes, also run:

```console
composer validate --strict
composer audit
composer --working-dir=_install audit
```

Add or update regression tests for bug and security fixes. Do not claim a check
passed if it was skipped, unavailable, or returned a non-zero status; report
environment-dependent integration failures separately from code failures.

## Git and public-repository hygiene

- Do not discard, overwrite, stage, or commit unrelated user changes.
- Keep each commit to one purpose. If a message needs “and” or “+”, split the
  commit unless the files are inseparable parts of one fix.
- Write commit subjects in the present tense. Keep them short, explicit, and
  clear about both the change and its motivation.
- Review the complete staged diff before committing. Public commits must exclude
  secrets, private audit material, runtime/user files, caches, and generated
  noise.
