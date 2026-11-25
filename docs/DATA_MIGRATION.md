# Data migration

pH7Builder can preview and import members from a canonical CSV file. The first
version-specific source adapter exports member accounts from UNA 14.0.0 without
modifying the UNA database.

This workflow first appears in pH7Builder 20.0.0. It is not available in 19.4.0
or earlier releases.

This is an incremental migration path, not a claim that every feature of a
source platform is portable. Test the complete workflow on an isolated staging
copy before importing production data.

## Supported paths

| Source | Validated scope | Status |
| --- | --- | --- |
| Generic CSV | Member accounts and basic profile fields | Available in the administration panel |
| UNA 14.0.0 | Accounts and Persons profiles exported to canonical CSV | Available through the read-only CLI exporter |
| Other platforms or UNA versions | None | Not supported until a version-specific adapter and representative fixture are tested |

The current importer does not migrate photos, albums, conversations, friends,
subscriptions, transactions, or payment credentials. Never move payment
credentials between systems. Add a source feature only after its schema,
privacy rules, ownership, and destination behaviour have representative tests.

## Export UNA 14.0.0 members

Create a dedicated UNA database user with `SELECT` access only. Do not reuse a
production application or database administrator account.

Set connection details in environment variables so the password is not stored
in shell history:

```console
export PH7_MIGRATION_SOURCE_DSN='mysql:host=127.0.0.1;dbname=una;charset=utf8mb4'
export PH7_MIGRATION_SOURCE_USER='una_migration_reader'
export PH7_MIGRATION_SOURCE_PASSWORD='replace-with-the-read-only-password'
```

Run the compatibility check and preview first. It reads every matching source
row in bounded batches but writes no file and changes neither database:

```console
php _tools/cli/ph7cms migration:una:export
```

Create a new owner-readable CSV only after the preview succeeds:

```console
php _tools/cli/ph7cms migration:una:export \
  --output=/secure/private/path/una-members.csv
```

Useful controls:

- `--batch-size=500` controls source rows read at once, from 1 to 5000.
- `--after-id=12345` resumes from the next UNA account ID into a new CSV.
- `--limit=1000` caps the number of valid members written for a staged trial.
- `--table-prefix=example_` supports installations that prefix UNA tables.

The exporter verifies the UNA 14.0.0 account, profile, and Persons columns
before reading data. Output creation uses no-overwrite mode and file permission
`0600`. The source transaction is read-only.

## Preview and import in pH7Builder

1. Back up the destination database and restore it into staging.
2. Open **Administration > Users > Import Users**.
3. Upload the CSV and keep **Preview only** selected.
4. Review the importable, invalid, and duplicate counts.
5. Re-upload the unchanged CSV and select **Import after reviewing a preview**.
6. Verify member counts, sample profiles, privacy settings, and password recovery.
7. Remove the private CSV securely after acceptance.

The importer processes one row at a time. Invalid emails are skipped, duplicate
emails in the file or destination are not imported, and each member creation
uses the existing transactional registration model. A repeated CSV therefore
skips members already created by email rather than duplicating them.

## Password and account safety

Source password hashes are deliberately omitted. Credential formats and
security parameters differ between platforms, and copying an unverified hash
would risk account takeover or silent login failures. pH7Builder creates a
unique random password for every imported account. Members must use the normal
password-recovery flow before signing in.

UNA accounts are active only when the source account is active, its email is
confirmed, its Person profile is active, and supported gender and birth-date
values are present. Other source accounts enter pH7Builder's manual-approval
state.

## Field mapping and limitations

The UNA exporter maps verified fields from `sys_accounts`, `sys_profiles`, and
`bx_persons_data`. It preserves the source account ID in the CSV for audit and
recovery, but pH7Builder assigns its own profile ID. Location is not mapped yet
because UNA storage can depend on installed modules and structured location
data. Unknown genders fall back to the destination's current supported default.

The generic CSV importer recognises common aliases for email, username, names,
gender, matching preference, birth date, description, country, city, region,
postal code, IP address, and activation state. Extra columns are ignored.
Canonical adapter exports include source provenance columns. In those files,
intentional empty values remain empty instead of receiving the generic CSV
importer's legacy sample defaults. An unavailable birth date remains unset.

## Compatibility policy

The UNA 14.0.0 adapter is possible because its MIT-licensed schema is public and
can be represented by a version-specific test fixture. The adapter verifies the
required tables and columns before exporting anything, but it does not imply
compatibility with another UNA release or a customised schema.

Do not claim support for another source platform or version until its schema,
field mapping, privacy constraints, failure recovery, and destination behaviour
have representative automated tests. Commercial platforms such as SkaDate and
Dating Pro require a lawfully obtained, version-specific fixture before an
adapter can be designed or advertised.

## Commercial boundary

The core preview, canonical CSV format, and validated adapters remain part of
the free migration path. A future paid offering may provide an asynchronous
compatibility report for a specific customised installation, but it must not
withhold a working standard migration or turn security and data-integrity fixes
into paid features.
