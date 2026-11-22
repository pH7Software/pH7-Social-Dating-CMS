---
name: ph7builder-upgrade-preflight
description: Assess a pH7Builder version upgrade safely and prepare a read-only compatibility, backup and migration checklist.
---

# Safe pH7Builder upgrade assessment

Use this skill when asked whether an existing installation can upgrade or when designing upgrade tooling.

1. Read `AGENTS.md`, `docs/UPGRADING.md`, `docs/QUICK_START.md`, the relevant release notes and `docs/LAUNCH_CHECKLIST.md`.
2. Determine the *installed* application and schema versions, PHP/MySQL versions, custom database prefix, enabled modules, themes and local modifications. If any are unknown, list the missing facts instead of guessing.
3. Identify every required intermediate migration and the exact supported target release. The current repository documentation says automatic in-place upgrades are unavailable.
4. Prepare a **read-only** preflight report: environment compatibility, backups to take, areas requiring manual review, proposed staging tests and a rollback plan.
5. Never run schema migrations, overwrite files, alter live configuration, disable payments or delete the installer on a real instance without explicit authorisation and a verified recovery path.
6. Restore a backup into isolated staging before testing an upgrade. Keep application files, database migrations, uploads and configuration paired to the correct version.
7. Record outcomes for registration, login, messaging, moderation, emails, cron, payments and any local extensions. If checks are unavailable, report them as unverified.

A successful preflight is not proof that production upgrades are safe. Do not describe one-click automatic upgrades as an existing capability.
