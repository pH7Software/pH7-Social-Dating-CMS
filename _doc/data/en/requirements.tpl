<h1>Requirements for the pH7 &quot;Dating Community Social&quot; software</h1>

<h2>Server:</h2>
<h3>Minimum:</h3>
<ul>
    <li>Operating System - Linux/Unix (Red Hat, CentOS, Debian, FreeBSD, Mandrake, Mac OS, etc.), Windows.</li>
    <li>Web Server - Apache with mod_php enabled, PHP in CGI, FastCGI mode, or with <a
                href="http://ph7cms.com/doc/en/nginx-configuration">nginx</a> (lighttpd, LiteSpeed and IIS should also
        work but you have to change some piece of code to make it working).
    </li>
    <li>URL rewriting extension module - Apache, nginx, lighttpd, LiteSpeed, IIS (for Web.config, you have a <a
                href="http://www.phpgenious.com/2010/04/url-rewriting-with-php-and-iis-7/">good tutorial here</a>).
    </li>
    <li>Minimum Web Space - 2.0 GB</li>
    <li>Specific Requirement - Server has to be connected to Internet</li>
    <li>Video Module Requirement (only if enabled) - <a href="http://ffmpeg.org">FFmpeg</a></li>
</ul>

<h3>Recommended:</h3>
<ul>
    <li>JRE (Java Runtime Environment) - 1.6 or higher (used for compressing static files. The option is disabled by
        default and uses our homemade compressor instead. However, if you have Java installed on your server, it would
        be nicer to enable that option).
    </li>
    <li>Apache mod_security/mod_security2 disabled - pH7CMS may not work correctly if enabled, so it's advisable to disable it for
        your domain (just ask your Web host or do it through cPanel).
    </li>
</ul>


<h2>PHP:</h2>
<h3>Minimum:</h3>
<ul>
    <li>Version - 5.6 or higher</li>
    <li>PDO extension</li>
    <li>GD complied with your PHP Build</li>
    <li>PHP CURL extension</li>
    <li>Zip compression PHP module</li>
    <li>Zlib compression PHP module</li>
    <li>mbstring PHP module</li>
    <li>exif PHP extension</li>
    <li>dom PHP extension</li>
    <li>xml PHP extension</li>
    <li>Send Mail PHP activated</li>
    <li>memory_limit - 128M or higher</li>
    <li>max_input_time - -1 (Unlimited)</li>
    <li>post_max_size - 100M or higher</li>
    <li>upload_max_filesize - 100M or higher</li>
    <li>file_uploads - On</li>
    <li>allow_url_fopen - On</li>
    <li>allow_url_include - Off</li>
    <li>
        exec() function is needed if "Video" module is enabled (to execute FFmpeg program),<br />
        exec() function is needed if you use the "Upgrade"/"3rd-party Mods Manager" installation wizard,<br />
        exec() function is needed if the minify Java compiler is enabled (disabled by default).
    </li>
</ul>

<h3>Recommended:</h3>
<ul>
    <li><span class="bold">PHP 7.0.4</span> or higher
        <small>(pH7CMS has been especially optimized for PHP 7+ and is about over 2x faster than older versions. Please
            note your server configuration can also change a lot your site performance).
        </small>
    </li>
</ul>

<h3>Recommended Extensions:</h3>
<ul>
    <li>
    <li>APC module (for speed boost)</li>
    <li>Gettext PHP extension (for better stability and optimization)</li>
    <li>OpenSSL (for the "Connect" module)</li>
    <li>iconv module</li>
</ul>


<h2>MySQL or MariaDB</h2>
<h3>Minimum:</h3>
<ul>
    <li>Version - 5.0.15 or higher</li>
    <li>InnoDB table support</li>
</ul>
<h3>Recommended:</h3>
<ul>
    <li>MySQL/MariaDB 5.5 or higher</li>
</ul>
