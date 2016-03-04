# pH7 Social Dating CMS

### The Most Secure and Powerful Professional Dating Web App Builder


[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/pH7Software/pH7-Social-Dating-CMS/badges/quality-score.png?s=79700cb86e25e0f125926f2f52984dd3ebacd81d)](https://scrutinizer-ci.com/g/pH7Software/pH7-Social-Dating-CMS/)


**pH7 Social Dating CMS** is a **Professional** & **Open Source** Social Dating CMS, fully responsive design, low-resource-intensive, powerful and very secure.

pH7CMS is included with 30 modules and based on its homemade framework (pH7Framework). It is also the first Professional, free and open source European Social Dating Site Builder Software and the first choice for creating enterprise level Dating Apps/Service or social networking sites.


![Professional PHP Social Networking Dating CMS](https://cloud.githubusercontent.com/assets/1325411/12043273/747be578-ae7b-11e5-84cb-f1724ebc969d.png)


## Overview

**pH7 Dating CMS** is a **Social/Dating CMS** written in **Object-Oriented** PHP (*OOP*), fully compatible and optimised for PHP 7+ and based on **MVC** architecture (Model-View-Controller).

It is designed with the **KISS** principle in mind, and the all source code can be read and understood in minutes. For a better flexibility, the software uses **PDO** (PHP Data Objects) abstraction which allows the choice of the database. The principle of development is **DRY** (Don't Repeat Yourself) aimed at reducing repetition of information of all kinds (not duplicate code).

This Free and Open Source **Social Dating Site Builder** wants to be low resource-intensive, powerful, stable and secure. The software also comes with 30 system modules and is based on **pH7Framework** *(written specifically for this project)* that has over 52 packages.

To summarize, **pH7CMS** gives you **the perfect ingredients** to create the **best dating service** or **social networking** website on the World Wide Web!


## How Powerful is your future Dating App?

* Best Dating Features
* Advanced Search
* Blogs
* Notes
* Pages Management
* Friends, Visit, Messages, Instant messaging, Views, Like, Rating, Smileys, Geo Map, Avatar, Wallpaper, ...
* Custom Profile (Background profile)
* Comments
* Hot or Not
* Love Calculator
* Geolocation
* Photo Albums
* Videos (and possibility to upload videos from YouTube, Vimeo, Metacafe and Dailymotion)
* Forums
* Content Moderation
* Watermark Branding
* Chat Rooms
* Chatroulette
* Games (with high quality and viral games installed)
* Webcam Shot
* Affiliate
* Newsletter
* Activity Streams
* Member approval system
* Advanced Admin Panel
* Complete Membership System
* Payment Gateways Integration for PayPal, Stripe, Bitcoin and 2CheckOut
* Statistics & Analytics System
* Live Notification System
* Registration delay (to avoid spam)
* File Management
* Dynamic Field Forms Management
* Privacy Settings
* Banner/Advertisement Management
* Support for Multiple Languages, Internationalization and Localization (I18N)
* American & European for the Time and Date formats
* Cache system for the database, pH7Tpl (our template engine), static files (HTML, CSS, JS), string content, ...
* Maintenance Mode
* Database Backup
* Report
* SEO-Friendly (Title, Content, Code, ...), Sitemap module, [hreflang](https://support.google.com/webmasters/answer/189077), possibility to translate each URL, ...
* Multilingual URLs
* Check that all UGC (User-Generated Content) is Unique (to avoid spam and malicious users)
* RSS Feed
* Fully API for integration from an external app (iOS/Android, ...), website, program, ...
* Feedback
* Fully Responsive Templates
* Multiple-Themes and many customization possible
* Message templates
* Multi Themes and many personalizable
* Includes top HTML5 features
* Facebook, Google, Twitter connect
* Invite Friends
* Social Bookmark
* Anti-spam system
* Beautiful Code: Very thoroughly commented about what's happening throughout the PHP code, beautiful indentation and very readable, even for non-programmers
* Everyone can easily contribute to this great innovative project with our [GitHub repository](http://github.com/pH7Software/pH7-Social-Dating-CMS)


**It's not a hazard that pH7CMS is considered to be the first choice for creating enterprise level Dating Apps/Service or Social Networking Sites**

*If you need, [here is some features](http://ph7cms.com/social-dating-features/) included in pH7CMS (and obviously in [pH7CMS Pro](http://ph7cms.com/pro/) as well) that may interest you!*

## Help Us to Grow Up

**It's 100% Open Source & Free**

**It's an Awesome Project.**

**We want You to Make It the Best!**


If you want to work on an Innovative and Exciting Project with a Beautiful PHP code using the latest PHP features while collaborating with nice people, ... So You Have to **Join Us**!

Send an email at: *hello {AT} ph7cms {D0T} com* and Start a Wonderful Adventure!


Thank you so much in advance!


## Requirements

**Application Server** PHP 5.5.0 or higher.

**Database** MySQL/MariaDB 5.0.15 or higher.

**Operating System** Linux/Unix (Red Hat, CentOS, Debian, FreeBSD, Mandrake, Mac OS, etc.), Windows.

**Web Server** Apache with mod_php or with PHP in CGI, FastCGI mode (nginx, LiteSpeed and IIS should also work you should change some pieces of code and change the url rewriting to make it work).

**URL rewriting extension module** Apache, nginx, LiteSpeed, IIS (for Web.config, you have a [good tutorial here](http://www.phpgenious.com/2010/04/url-rewriting-with-php-and-iis-7/)).

**Video** [FFmpeg](http://ffmpeg.org)

**Minimum Web Space** 2.0 GB


## Nginx Configuration

In order to get pH7CMS working on nginx server, you need to add some custom nginx configuration.

Create `/etc/nginx/ph7cms.conf` and add the following:

```nginx
location / {
    try_files $uri $uri/ /index.php?$args;
    index index.php;
}
```

*Please note that the above code is the strict minimum and obviously you can add more by comparing with the [main Apache .htaccess file](https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/master/.htaccess).*


Now in your nginx server configuration you will have to include `ph7cms.conf` file to complete the configuration like below:

In file, e.g., *`/etc/nginx/sites-enabled/yoursite.conf`* for Ubuntu and other OS based on Debian or `/etc/nginx/conf.d/yoursite.conf` for CentOS and other OS based on Red Hat.

```nginx
server {
    # Port number. In most cases, 80 for HTTP and 443 for HTTPS
    listen 80;

    server_name www.yoursite.com;
    root /var/www/ph7cms_public_root;
    index index.php; #you can use index.ph7; for hidding the *.php ...
    client_max_body_size 50M;

    error_log /var/log/nginx/yoursite.error.log;
    access_log /var/log/nginx/yoursite.access.log;

    # Include ph7cms.conf. You can also directly add the "location" rule instead of including the conf file
    include /etc/nginx/ph7cms.conf;
}
```

For more information, please refer to the nginx documentation.


## Hosting

Recommended Hosting for **pH7CMS**

<p><a href="http://ph7cms.com/web/arvandixe"><img src="http://ph7cms.com/web/arvandixeimg" width="250" height="250" alt="Arvixe: Recommended for pH7CMS" title="Arvixe: Recommended for pH7CMS"></a> &nbsp; <a href="http://ph7cms.com/web/hostupon"><img src="http://ph7cms.com/web/hostuponimg" width="250" height="250" alt="HostUpon: Recommended for pH7CMS" title="HostUpon: Recommended for pH7CMS"></a></p>
<p><a href="http://ph7cms.com/web/tmdhost"><img src="http://ph7cms.com/web/tmdhostimg" width="250" height="250" alt="TMD Hosting: Recommended for pH7CMS" title="TMD Hosting: Recommended for pH7CMS"></a> &nbsp; &nbsp; <a href="http://ph7cms.com/web/hostforweb"><img src="http://ph7cms.com/web/hostforwebimg" width="250" height="250" alt="HostForWeb: Recommended for pH7CMS" title="HostForWeb: Recommended for pH7CMS"></a></p>
<p><a href="http://ph7cms.com/web/faction"><img src="http://ph7cms.com/web/factionimg" width="250" height="250" alt="WebFaction: Recommended for pH7CMS" title="WebFaction: Recommended for pH7CMS"></a> &nbsp; <a href="http://ph7cms.com/web/rackhost"><img src="http://ph7cms.com/web/rackhostimg" width="250" height="250" alt="RackSpace: Recommended for pH7CMS" title="RackSpace: Recommended for pH7CMS"></a></p>


## Translations

You can find and add other languages on the [I18N repo](https://github.com/pH7Software/pH7-Internationalization).


## Author

Pierre-Henry Soria


## Website

**[Social Dating Script](http://ph7cms.com)**


## Documentation

[pH7CMS Documentation](http://ph7cms.com/doc/)


## Contribute!

Everyone can contribute on the **[pH7CMS GitHub](http://github.com/pH7Software/pH7-Social-Dating-CMS)** repository.

Just clone the repository, make your changes and then make a push ;-)

*WARNING, your code/modification must be of excellent quality.*

--We manually validate all the improvements and changes.--


## Contact

You can send email for any suggestions or comments at: *hello {AT} ph7cms {D0T} com*


## Help & Modifications

If you want, we also offer a *Premium Commercial* support at *[HiZup Support Department](http://clients.hizup.com/support)*


## License

**pH7CMS** is under **Open Source Free** License.

License: [General Public License 3](http://www.gnu.org/licenses/gpl.html) or later; See the *PH7.LICENSE.txt* and *PH7.COPYRIGHT.txt* files for more details.
