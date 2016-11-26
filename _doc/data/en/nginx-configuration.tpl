<h2>Nginx Configuration</h2>

<p>In order to get <a href="http://ph7cms.com">pH7CMS</a> working on nginx server, you need to add some custom nginx configuration.</p>

<p>Create "<em>/etc/nginx/ph7cms.conf</em>" and add the following:</p>

<pre>
<code class="nginx">
location / {
    try_files $uri $uri/ /index.php?$args;
    index index.php;
}
</code>
</pre>

<p><em>Please note that the above code is the strict minimum and obviously you can add more by comparing with the <a href="http://github.com/pH7Software/pH7-Social-Dating-CMS/blob/master/.htaccess">main Apache .htaccess file</a></em></p>

<p>Now in your nginx server configuration you will have to include `ph7cms.conf` file to complete the configuration like below:</p>

<p>In file, e.g., "<em>/etc/nginx/sites-enabled/yoursite.conf</em>" for Ubuntu and other OS based on Debian or "<em>/etc/nginx/conf.d/yoursite.conf</em>" for CentOS, Fedora and other OS based on Red Hat.</p>

<pre>
<code class="nginx">
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
</code>
</pre>

<p>For more information, please refer to the nginx documentation.</p>