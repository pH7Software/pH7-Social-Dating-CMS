# Contributing to pH7Builder 💖

Thank you for helping to make pH7Builder the best free dating/social web app builder! Any contribution — bug fixes, features, docs, translations — is welcome and highly appreciated.

## Quick start (development setup)

```console
git clone https://github.com/pH7Software/pH7-Social-Dating-CMS.git
cd pH7-Social-Dating-CMS
docker compose up --build
docker compose exec php php _install/create-install-token.php
```

Enter the printed token at <http://localhost:8080> to run the installer. The development database uses
host `db`, database and username `ph7builder`, and password `ph7builder`.
Because Docker snapshots the checkout into a named volume, run
`docker compose down -v` before rebuilding after source changes. This deletes
the local application and database volumes.

Without Docker: install dependencies with `composer install`, then use any PHP
>= 8.2 installation with the extensions listed in `composer.json`,
MySQL 8.0+, and Apache/nginx (see `nginx.conf` / `sample.htaccess`).

## Before you open a pull request

Open pull requests against `18.x`, the current active default branch. The
software version is defined by release tags and the version constants, not by
the branch name. Develop each change on a focused, short-lived branch; once its
pull request is merged, that working branch can be deleted.

1. **Follow the [Code Convention](https://ph7builder.com/doc/en/code-convention).** The short version:
   - Variables use camelCase with a type-prefix (Hungarian notation): `$sName` (string), `$iCount` (int), `$aItems` (array), `$oUser` (object), `$bEnabled` (bool), `$mValue` (mixed), `$fPrice` (float).
   - Framework classes end in `.class.php`, traits in `.trait.php`, interfaces in `.interface.php` (with a `-ble` name when possible). App classes under `_protected/app` are plain `.php`.
   - Constants are `ALL_CAPS`; functions and asset files are `lowercase_with_underscores`.
2. **Run the code style fixer** on the files you touched:
   ```console
   _protected/vendor/bin/php-cs-fixer fix --path-mode=intersection <changed files>
   ```
3. **Run the tests and static analysis** — both must stay green:
   ```console
   composer test      # deterministic PHPUnit suite
   composer analyse   # PHPStan
   ```
   The separate `composer test-integration` suite requires an installed local
   application and the external services or API credentials exercised by those
   tests.
4. Keep commits focused and their messages descriptive. Small, reviewable pull requests get merged faster.

Theme/CSS contributions: `_tools/theme-preview.html` renders the base theme's CSS chain against sample markup without a full install — serve the repo root (e.g. `python3 -m http.server 8642`) and open it to check your changes, in both light and dark mode.

For real PFBC forms, run `php -S 127.0.0.1:8642 -t .` and open
`http://127.0.0.1:8642/_tools/pfbc-preview.php`. Check base and premium themes at
desktop and mobile widths, then select **Run interaction checks** to verify
agreement handling, password visibility, age sliders, character counts, and Ajax retry.
This preview only runs on PHP's local development server and uses no application data.

Use `_tools/autocomplete-preview.html` on the same server to check recipient and
city suggestions. Its interaction checks use fixed mock responses; they do not
contact members or GeoNames and do not verify the external provider's availability.

## Reporting bugs & proposing features

- Bugs: use the [issue tracker](https://github.com/pH7Software/pH7-Social-Dating-CMS/issues) and the bug-report template.
- Ideas and questions: the [discussions board](https://github.com/pH7Software/pH7-Social-Dating-CMS/discussions).
- Security vulnerabilities: please do **not** open a public issue — see [SECURITY.md](SECURITY.md).

All contributions are manually reviewed. By contributing, you agree your code is released under the project's [MIT License](LICENSE.md).
