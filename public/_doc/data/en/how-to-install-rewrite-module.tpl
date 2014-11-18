<h2>How to install rewrite module on your Web server</h2>

<p>Once you have successfully installed the Apache server and the "mod_rewrite" module, you have to edit the Apache configuration file.</p>
<p>The Apache configuration file depends on your operating system.<br />
For CentOS and other OS based on Red Hat, the Web server configuration file will normally be "<em>/etc/httpd/conf/httpd.conf</em>".<br />
For Ubuntu and other OS based on Debian, the Web server configuration file will normally be "<em>/etc/apache2/sites-enabled/000-default.conf</em>".</p>
<p>Now change "<em>AllowOverride None</em>" to "<em>AllowOverride All</em>" inside the DocumentRoot Directory Directive, normally "<em>&lt;Directory "/var/www"&gt;</em>"
<p>Now, save the file (you need to have the administrative privileges), and restart your Apache server.</p>
<p>Done!</p>

<p>I hope this help will be useful and will save you much time.</p>
<p>P.S., Normally, all shared hosts are already configured to work with Apache mod_rewrite and allow configuration via .htaccess</p>
