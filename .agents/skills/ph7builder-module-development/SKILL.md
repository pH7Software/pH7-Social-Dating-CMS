---
name: ph7builder-module-development
description: Implement or review a pH7Builder custom module while preserving existing MVC, routing and security conventions.
---

# Building pH7Builder modules

Use this skill for customer-specific functionality, new modules, or enhancements to existing modules.

1. Read `AGENTS.md`, `CONTRIBUTING.md`, and adjacent code. Inspect `_protected/app/modules/helloworld` before designing a new custom module.
2. Place changes in the narrowest owning layer. Prefer `_protected/app/modules/` for optional modules; do not add a parallel framework or bypass the existing module/router architecture.
3. Follow the repository's class, PHP file, PH7Tpl, route, and database naming rules. Use `Uri::get()` and the XML routing model instead of hardcoded application paths.
4. Validate permissions, ownership and CSRF for state-changing actions. Use prepared SQL statements and context-correct escaping; treat user and stored data as untrusted.
5. Preserve the MIT-licensed free core and its existing features. Do not introduce commercial feature restrictions as an incidental part of module work.
6. For schema changes, check both fresh-install SQL and the correct versioned migration path. Preserve custom table prefixes.
7. Add focused behavioural tests. Run applicable `php -l`, PHPUnit, PHPStan, formatting and `git diff --check` commands when available. Report skipped checks honestly.

Do not refactor unrelated modules or commit secrets, user content, logs or generated runtime files.
