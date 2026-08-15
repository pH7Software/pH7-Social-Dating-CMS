# Production Quick Start

This guide takes a prepared Linux server from a clean checkout to a working
pH7Builder site. It assumes PHP, MySQL, the web server, and DNS are already
available. Under those conditions, dependency installation and the guided
installer should take about 20–30 minutes. DNS propagation, TLS certificate
issuance, and mail-provider approval can take longer.

For a local evaluation instead, use the Docker procedure in the
[README](../README.md#local-evaluation-with-docker).

## 1. Confirm the requirements

- PHP 8.2 or newer.
- MySQL 8.0 or newer. Older MySQL versions and MariaDB are unverified and are
  not supported for this release.
- PHP extensions: cURL, DOM, EXIF, Fileinfo, GD with FreeType and WebP, hash,
  iconv, JSON, mbstring, OpenSSL, PDO MySQL, SimpleXML, XML, XMLWriter, ZIP,
  and zlib.
- nginx or Apache, URL rewriting, and HTTPS for production. Composer 2 is
  needed only when deploying from a source checkout.
- A working server/hosting sendmail transport, or SMTP credentials supplied as
  `PH7_MAILER_DSN` to the PHP web process.

Check the important values before downloading the application:

```console
php -v
php -m
mysql --version
php -r '$g=gd_info(); printf("upload=%s post=%s freetype=%s webp=%s\n", ini_get("upload_max_filesize"), ini_get("post_max_size"), !empty($g["FreeType Support"])?"yes":"no", !empty($g["WebP Support"])?"yes":"no");'
```

The browser installer repeats its PHP, extension, permission, MySQL-version,
and database checks before it writes the schema.

## 2. Download the verified release package

The versioned release ZIP includes locked production dependencies. Download
the archive and its checksum from the same GitHub release:

```console
sudo mkdir -p /var/www/ph7builder
sudo chown deploy:www-data /var/www/ph7builder
cd /tmp
curl -LO https://github.com/pH7Software/pH7-Social-Dating-CMS/releases/download/v18.6.1/pH7Builder-v18.6.1.zip
curl -LO https://github.com/pH7Software/pH7-Social-Dating-CMS/releases/download/v18.6.1/pH7Builder-v18.6.1.zip.sha256
sha256sum -c pH7Builder-v18.6.1.zip.sha256
unzip pH7Builder-v18.6.1.zip
cp -a pH7Builder-v18.6.1/. /var/www/ph7builder/
```

Run these commands as the `deploy` account, or replace that example account
with your normal non-root deployment user.

For a source checkout instead, clone tag `v18.6.1` and run:

```console
git clone --branch v18.6.1 --depth 1 https://github.com/pH7Software/pH7-Social-Dating-CMS.git pH7Builder-v18.6.1
cd pH7Builder-v18.6.1
composer install --no-dev --prefer-dist --optimize-autoloader
```

As another verified path, Composer can create the project directly from the
tagged Packagist release. Run this from the parent of the intended deployment
directory:

```console
composer create-project ph7software/ph7builder:18.6.1 ph7builder --no-dev --prefer-dist
```

Softaculous and SourceForge also list pH7Builder, but their available archive
may lag behind the current GitHub and Packagist release. Confirm the exact
version before using either channel.

If Composer warns about running as root, stop and correct the directory owner
before continuing.

## 3. Create the MySQL database

Choose a unique password and keep it outside the repository. The following is
for a local MySQL server:

```sql
CREATE DATABASE ph7builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ph7builder'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_LONG_RANDOM_PASSWORD';
GRANT ALL PRIVILEGES ON ph7builder.* TO 'ph7builder'@'localhost';
FLUSH PRIVILEGES;
```

Run it with an administrative MySQL account:

```console
sudo mysql
```

For a managed database, use its hostname, port, database name, TLS policy, and
credentials. Do not reuse the database administrator account in pH7Builder.

## 4. Set narrow filesystem permissions

The deployment account should own the source, and the PHP-FPM/Apache group
must write only the paths used for configuration and runtime data. This
Debian/Ubuntu example assumes deployment user `deploy` and web group
`www-data`; adapt both names to your server:

```console
sudo chown -R deploy:www-data /var/www/ph7builder
sudo find /var/www/ph7builder -type d -exec chmod 0755 {} +
sudo find /var/www/ph7builder -type f -exec chmod 0644 {} +
sudo chgrp www-data /var/www/ph7builder
sudo chmod 0775 /var/www/ph7builder
sudo chgrp www-data /var/www/ph7builder/_protected/app/configs
sudo chmod 0775 /var/www/ph7builder/_protected/app/configs
sudo chgrp -R www-data /var/www/ph7builder/_install/data/caches /var/www/ph7builder/_install/data/logs /var/www/ph7builder/_protected/data/cache /var/www/ph7builder/_protected/data/log /var/www/ph7builder/_protected/data/tmp /var/www/ph7builder/_protected/data/backup /var/www/ph7builder/data /var/www/ph7builder/_repository/module
sudo chgrp -R www-data /var/www/ph7builder/_install
sudo find /var/www/ph7builder/_install/data/caches /var/www/ph7builder/_install/data/logs /var/www/ph7builder/_protected/data/cache /var/www/ph7builder/_protected/data/log /var/www/ph7builder/_protected/data/tmp /var/www/ph7builder/_protected/data/backup /var/www/ph7builder/data /var/www/ph7builder/_repository/module -type d -exec chmod 0775 {} +
sudo find /var/www/ph7builder/_install/data/caches /var/www/ph7builder/_install/data/logs /var/www/ph7builder/_protected/data/cache /var/www/ph7builder/_protected/data/log /var/www/ph7builder/_protected/data/tmp /var/www/ph7builder/_protected/data/backup /var/www/ph7builder/data /var/www/ph7builder/_repository/module -type f -exec chmod 0664 {} +
sudo find /var/www/ph7builder/_install -type d -exec chmod 0775 {} +
sudo chmod 2775 /var/www/ph7builder/_install/data/caches
sudo find /var/www/ph7builder/_protected/app/configs/banned /var/www/ph7builder/_protected/app/configs/suggestions -type f -name '*.txt' -exec chmod 0660 {} +
sudo find /var/www/ph7builder/_protected/app/configs/routes -type f -name '*.xml' -exec chmod 0660 {} +
sudo find /var/www/ph7builder/_protected/app/system/global/views/base/tpl/mail /var/www/ph7builder/_protected/app/system/modules/page/views/base -type f -name '*.tpl' -exec chmod 0660 {} +
sudo find /var/www/ph7builder/templates/themes -type f \( -name '*.tpl' -o -name '*.css' -o -name '*.js' \) -exec chmod 0660 {} +
sudo chgrp www-data /var/www/ph7builder/_protected/app/system/modules/affiliate/config/config.ini /var/www/ph7builder/_protected/app/system/modules/payment/config/config.ini /var/www/ph7builder/_protected/app/system/modules/sms-verification/config/config.ini /var/www/ph7builder/_protected/app/system/modules/video/config/config.ini
sudo chmod 0660 /var/www/ph7builder/_protected/app/system/modules/affiliate/config/config.ini /var/www/ph7builder/_protected/app/system/modules/payment/config/config.ini /var/www/ph7builder/_protected/app/system/modules/sms-verification/config/config.ini /var/www/ph7builder/_protected/app/system/modules/video/config/config.ini
```

Do not use `chmod 777`. The installer temporarily needs group write access to
the application root and `_protected/app/configs` so it can create
`_constants.php` and atomically install `config.ini`; remove that directory
access during the lockdown step below.

## 5. Configure the canonical host, HTTPS, and web server

### nginx

Start from the tracked [`nginx.conf`](../nginx.conf). Replace its example
domain, document root, log paths, and PHP-FPM socket. Keep every protected-path
rule, reject unknown hostnames, and allow only `/index.php` and
`/_install/index.php` to execute.

```console
sudo nginx -t
sudo systemctl reload nginx
```

### Apache

Enable URL rewriting, make the site root the document root, and allow the
tracked `.htaccess` rules for that directory:

```console
sudo a2enmod rewrite headers
sudo apachectl configtest
sudo systemctl reload apache2
```

The tracked nginx examples accept requests up to 50 MB, while the fresh video
setting is 45 MB. Set PHP-FPM `upload_max_filesize` to at least `50M` and
`post_max_size` slightly higher (for example `52M`), then restart PHP-FPM.
If you choose another limit, keep the web server, PHP, and Admin video setting
consistent and test both an accepted file and an oversized rejection.

Set the virtual host's canonical `ServerName`/`server_name`, point DNS to the
server, and issue the TLS certificate **before** installation. For example,
after the nginx HTTP virtual host resolves correctly:

```console
sudo certbot --nginx -d example.com -d www.example.com
```

The installer persists its canonical scheme, hostname, optional port, and
protected-directory path in `_constants.php`. The safest path is therefore to
install once on the final HTTPS hostname.

When PHP runs behind a trusted reverse proxy, set the external authority in the
PHP-FPM environment before installing:

```ini
env[PH7_CANONICAL_HOST] = "example.com"
env[PH7_TRUST_PROXY_HEADERS] = "1"
```

Enable `PH7_TRUST_PROXY_HEADERS` only when the proxy removes client-supplied
forwarded headers and writes its own. Include the external port in
`PH7_CANONICAL_HOST` when it is non-standard. Direct nginx/Apache deployments
normally need neither variable.

Create the out-of-band installer access token as the deployment user and keep
the printed value temporarily in a password manager:

```console
cd /var/www/ph7builder
php _install/create-install-token.php
```

Open `https://example.com/_install/`, enter that token, and complete every step.
The installer records non-secret progress so the same token can resume after a
browser close or session expiry. Use the MySQL
credentials from step 3. Leave the FFmpeg path blank unless a real executable
is installed and the video module will process local uploads.

## 6. Lock the installation down

On the final installer screen, record the admin URL and use **Remove installer
folder**. Then verify it is gone and remove temporary root write access:

```console
test ! -d /var/www/ph7builder/_install
sudo chmod 0755 /var/www/ph7builder
sudo chmod 0755 /var/www/ph7builder/_protected/app/configs
sudo chown deploy:www-data /var/www/ph7builder/_constants.php
sudo chmod 0640 /var/www/ph7builder/_constants.php
sudo chown deploy:www-data /var/www/ph7builder/_protected/app/configs/config.ini
sudo chmod 0660 /var/www/ph7builder/_protected/app/configs/config.ini
sudo chmod 0660 /var/www/ph7builder/_protected/app/system/modules/affiliate/config/config.ini /var/www/ph7builder/_protected/app/system/modules/payment/config/config.ini /var/www/ph7builder/_protected/app/system/modules/sms-verification/config/config.ini /var/www/ph7builder/_protected/app/system/modules/video/config/config.ini
```

Only the explicitly listed configuration and editor target files remain
group-writable because their admin screens update them. Their directories stay
read-only to the web process after installation. Executable configuration PHP
files remain `0644`; `_constants.php` remains readable but not writable by the
web-server group.

If automatic removal fails, delete only the exact
`/var/www/ph7builder/_install` directory after confirming installation
completed and the path is correct. Do not leave the installer web-accessible.

Keep production error display disabled. Application logs belong under
`_protected/data/log/`, outside direct web access.

## 7. Configure cron

In Admin → Settings → Automation, replace the default cron secret with a long,
random value. Store it only in the server scheduler, then add the four jobs
below with your real HTTPS domain and URL-encoded secret:

```cron
5 2 * * * curl --fail --silent --show-error 'https://example.com/asset/cron/24h/Birthday/?secret_word=REPLACE_WITH_CRON_SECRET'
15 2 */4 * * curl --fail --silent --show-error 'https://example.com/asset/cron/96h/BannedIp/?secret_word=REPLACE_WITH_CRON_SECRET'
25 2 */4 * * curl --fail --silent --show-error 'https://example.com/asset/cron/96h/Database/?secret_word=REPLACE_WITH_CRON_SECRET'
35 2 */4 * * curl --fail --silent --show-error 'https://example.com/asset/cron/96h/General/?secret_word=REPLACE_WITH_CRON_SECRET'
```

The URL declares and enforces the minimum interval. Because calendar day
fields reset each month, an early `*/4` call can receive “already executed”;
the job will not run twice. Monitor cron output and application logs rather
than discarding errors.

## 8. Configure mail delivery

Admin → Settings → Email sets the sender name, admin address, feedback address,
and return address. It does **not** store an SMTP hostname, username, or
password.

For SMTP, set `PH7_MAILER_DSN` in the PHP web process environment. Use the DSN
given by the mail provider, with the username and password percent-encoded when
they contain reserved URL characters. Typical shapes are:

```text
smtp://SMTP_USER:SMTP_PASSWORD@smtp.example.com:587
smtps://SMTP_USER:SMTP_PASSWORD@smtp.example.com:465
```

For PHP-FPM, add the variable to the site pool configuration, not to source
control. A Debian/Ubuntu pool entry looks like this:

```ini
env[PH7_MAILER_DSN] = "smtp://SMTP_USER:SMTP_PASSWORD@smtp.example.com:587"
```

Reload the actual PHP-FPM service after changing its pool configuration; for
example, when the service is PHP 8.2:

```console
sudo systemctl reload php8.2-fpm
```

If `PH7_MAILER_DSN` is present but invalid or the SMTP connection fails,
pH7Builder records a transport error and does not silently send through another
transport. Correct the DSN or provider access before launch.

When `PH7_MAILER_DSN` is unset, pH7Builder uses the server/hosting sendmail
transport and can fall back to PHP `mail()`. Confirm that a local transport
exists:

```console
command -v sendmail
```

If you use this path, a direct sendmail test is:

```console
printf 'From: noreply@example.com\nTo: you@example.com\nSubject: pH7Builder mail test\n\nMail transport test.\n' | /usr/sbin/sendmail -t
```

Whichever transport you choose, enable email activation temporarily, register
a test member, and verify delivery, links, SPF, DKIM, DMARC, and spam-folder
placement. A successful SMTP response or `sendmail` exit status alone does not
prove inbox delivery.

## 9. Configure the business basics

1. Admin → Settings → General: site name, theme, homepage behavior, language,
   registration, security, spam, and moderation defaults.
2. Admin → Settings → Meta Tags/Homepage Texts: replace every placeholder with
   the actual brand and proposition.
3. Admin → Billing → Memberships List: verify the free and paid groups,
   permissions, prices, currencies, and expiry periods.
4. Admin → Billing → Gateways Configuration: keep unused gateways disabled,
   enter sandbox/test credentials first, and never commit credentials.
   Stripe is intentionally unavailable in 18.6.x because its bundled legacy
   flow is not SCA-ready; use another supported gateway until Stripe is
   migrated to Checkout Sessions or Payment Intents.
   The bundled 2Checkout flow is also unavailable until it is migrated to the
   2Checkout API 6.0.
   Braintree remains available, but its hosted
   [Drop-in UI](https://developer.paypal.com/braintree/docs/guides/drop-in/overview/javascript/v3)
   is scheduled for deprecation on September 1, 2026 and loss of support on
   September 1, 2027.
   Treat it as a short-term option and plan a supported Braintree checkout
   migration before those dates.
5. Complete one test purchase end to end before replacing sandbox credentials
   with live credentials.
   For PayPal, keep the generated `/payment/main/notify/paypal` HTTPS callback
   publicly reachable: pH7Builder supplies it as `notify_url`, verifies the IPN
   with PayPal, and fulfills the persisted checkout exactly once. Confirm that
   a duplicate sandbox IPN does not grant or commission the membership twice.
6. Admin → Tools → Backup Manager: create and restore a test backup; also
   configure independent off-server backups.

## 10. Move an evaluation installation to production

Installing directly on the final HTTPS host is safer. If an evaluation copy
must move, copy the files and database together, then edit only the generated
`_constants.php` values for the protocol, domain (including any non-standard
port), and protected absolute path. Never restore the old request-derived Host
logic. For example, the resulting values should describe the real deployment:

```php
$sUrlProtocol = 'https://';
$sDomain = 'example.com';
$sProtectedPath = '/var/www/ph7builder/_protected/';
```

After the move, make `_constants.php` read-only to the web process, clear caches
in Admin → Tools → Caches, and test redirects, cookies, password-reset and
activation links, uploads, email, and payment callbacks.

Cookies are host-only by default. Only sites that deliberately share login
state across subdomains should set a validated parent domain such as
`PH7_COOKIE_DOMAIN=example.com` in the PHP-FPM environment. Do not set this for
a single-host site.

Finally, run the complete [Launch Checklist](LAUNCH_CHECKLIST.md) before
accepting real registrations or payments.
