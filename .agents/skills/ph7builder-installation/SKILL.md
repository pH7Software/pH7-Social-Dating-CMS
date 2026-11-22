---
name: ph7builder-installation
description: Evaluate or install a published pH7Builder release locally or on a prepared production host.
---

# Installing or evaluating pH7Builder

Use this skill when setting up a local development copy, installing a published release, or preparing a deployment.

1. Read the repository `AGENTS.md`, `README.md`, `docs/QUICK_START.md`, and, for production, `docs/LAUNCH_CHECKLIST.md`. Treat these files and the published release notes as authoritative.
2. Distinguish the current source branch from the most recent **published** release. As of the documentation in this branch, 19.4.0 is published and 20.0.0 is being prepared. Check current release information rather than hardcoding an assumption.
3. For local evaluation, follow the README Docker procedure. Its database credentials and volume-recreation command are for disposable local environments only. Never reuse the example credentials or delete real data.
4. For production, check the exact release's PHP, MySQL, extensions, web-server, HTTPS and mail requirements before downloading anything. Verify the release archive checksum where provided.
5. Use the supported browser installer with its out-of-band access token. Never expose the installer or private directories after installation, and never use `chmod 777`.
6. Configure email, payments, scheduled jobs, permissions and off-server backups against the current release guide, not archived documentation.
7. Verify actual registration, authentication, messaging, moderation and, if enabled, payment journeys in the intended environment. Record any checks that could not be completed.

Do not install an unreleased branch on a production system or claim a deployment succeeded without verification.
