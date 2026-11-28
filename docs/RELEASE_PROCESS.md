# pH7Builder Release Process

Use this process for pH7Builder 20.0.0 and later releases. A release candidate
is not publishable until its exact commit passes the automated gate and the
deployment-specific checks below.

## Reliability freeze

Once a release candidate is declared, accept only fixes that improve
installation, upgrades, recovery, packaging, security, compatibility,
documentation, or a confirmed regression. Defer unrelated features to the
next development cycle. Restart the complete gate after every accepted change.

## Automated release gate

The gate requires PHP 8.3, Composer 2, MySQL 8.4, Chrome, Git, Zip, and the
MySQL client tools. Run it from a clean checkout with the v19.4.0 tag available:

```console
PH7_RELEASE_DB_ADMIN_USER=root \
PH7_RELEASE_DB_ADMIN_PASSWORD='local-root-password' \
composer release-gate
```

The command performs the following checks against one commit:

1. Validates and audits both Composer dependency locks.
2. Runs PHPStan and the deterministic PHPUnit suite.
3. Runs the responsive browser smoke journeys.
4. Loads the fresh schema.
5. Applies the 19.4.0-to-20.0.0 migration twice and checks preserved data.
6. Restores the pre-upgrade dump into a separate database and verifies it.
7. Builds the production ZIP from the committed tree.
8. Verifies archive exclusions, the SHA-256 checksum, and release metadata.
9. Installs the packaged ZIP through the complete browser wizard and verifies
   the resulting schema, administrator, sample profiles, homepage, and
   installer removal.
10. Signs in through the packaged member form and enforces query-count and
    warm-cache round-trip budgets on discovery, profile, dashboard, and inbox
    pages. The first request also verifies each journey while compiling caches.

The gate uses isolated temporary databases and removes them on exit. Set
`PH7_RELEASE_OUTPUT_DIRECTORY` to retain artefacts at a specific location. The
GitHub `Validate Release Candidate` workflow runs the same command and retains
successful candidate artefacts for 14 days.

## Automated stable publication

Once the selected `20.x` commit contains the final version metadata, dated
release notes, download references, and reviewed update feed, run the
`Publish Stable Release` workflow with that exact semantic version. The workflow
refuses a mismatched, existing, or undocumented version before it performs any
publication action.

The workflow then reruns the complete release gate, creates the tag and a draft
GitHub release from the verified commit, uploads the ZIP, checksum, SPDX SBOM,
and build metadata, and publishes the release only after every artefact is
available. It finally dispatches the release-feed deployment explicitly. This
explicit dispatch is required because GitHub does not start another workflow
from most events created with a repository's `GITHUB_TOKEN`.

The workflow dispatch is the deliberate release decision. Do not automate that
decision from every merge to `20.x`; routine pushes already build a candidate,
while a stable public release is irreversible for package consumers.

## Published artefacts

Publish these files together from the successful workflow run:

- `pH7Builder-vX.Y.Z.zip`
- `pH7Builder-vX.Y.Z.zip.sha256`
- `pH7Builder-vX.Y.Z.spdx.json`
- `pH7Builder-vX.Y.Z.build.json`

The SPDX document inventories locked production dependencies. The build
metadata records the commit, commit time, dependency-lock hashes, archive
hash, and SBOM filename. Artefact signing can be added after key custody and a
documented verification path exist; it must not replace checksum publication.

Before tagging, update the release date, release notes, Quick Start download
references, and `_tools/software-info.xml`. Keep `upd-alert` set to `false`
until the release and its upgrade instructions are publicly available. The
repository tests enforce runtime, installer, schema, build, and feed
consistency.

Publishing a stable GitHub release runs the `Publish Release Feed` workflow.
It checks out the published tag, refuses any tag that is not GitHub's latest
stable release, verifies that the feed version matches the tag, and deploys the
reviewed file to `xml.ph7builder.com` through GitHub Pages. The workflow can be
dispatched with the same latest stable tag to recover or repeat a deployment;
it must not be used to publish prereleases or older metadata.

The one-time Pages configuration belongs to this repository. Set its custom
domain to `xml.ph7builder.com`, point that subdomain directly to
`pH7Software.github.io` with a DNS-only CNAME record, and enable HTTPS after
GitHub provisions the certificate. Keep the existing endpoint and filename so
released installations do not need a compatibility redirect.

## Restore rehearsal

The automated gate proves that the stock 19.4.0 database dump can be restored.
Every real upgrade must also rehearse the operator's complete backup because
uploads, configuration, custom modules, themes, and database size are unique.

On an isolated staging environment:

1. Record the current application version, schema version, PHP/MySQL versions,
   enabled modules, and backup timestamp.
2. Restore the database, uploaded media, configuration, custom modules, custom
   themes, language packs, and credentials from the same backup set.
3. Keep external email, SMS, payment, and scheduled jobs disabled or sandboxed.
4. Verify administrator and member sign-in, representative profiles and media,
   search, messaging, module settings, and record counts.
5. Record the restore duration, failures, corrective steps, and the person who
   approved the rehearsal.
6. Apply the upgrade to this restored copy and complete the launch checklist.

Do not treat the presence of a backup file as recovery evidence. If the restore
or upgraded staging copy cannot be verified, stop the production deployment.

## Checks that remain deployment-specific

The repository gate cannot prove production DNS, TLS, reverse-proxy headers,
mail delivery, cron execution, object storage, custom code, live payment
providers, or production-scale performance. Complete
[the launch checklist](LAUNCH_CHECKLIST.md) on the target environment before
opening it to members or payments.
