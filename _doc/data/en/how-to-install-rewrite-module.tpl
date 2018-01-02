<h2>How to install Apache "mod_rewrite" on your Web server</h2>

<p>Once you have successfully installed the Apache server and the "mod_rewrite" module, you have to enable it with the following command in your terminal:</p>
<pre><code class="bash">sudo a2enmod rewrite</code></pre>
<p>Then you have to edit the Apache configuration file.</p>
<p>The Apache configuration file depends on your operating system.<br />
For CentOS, Fedora and other OS based on Red Hat, the Web server configuration file will normally be "<em>/etc/httpd/conf/httpd.conf</em>".<br />
For Ubuntu and other OS based on Debian, the Web server configuration file will normally be "<em>/etc/apache2/sites-enabled/000-default.conf</em>".</p>
<p>Now, change "<em>AllowOverride None</em>" to "<em>AllowOverride All</em>" inside the DocumentRoot Directory Directive, normally "<em>&lt;Directory "/var/www"&gt;</em>".<br />
If "<em>&lt;Directory "/var/www"&gt;</em>" isn't present, add <code class="apache">&lt;Directory "/var/www"&gt;AllowOverride All&lt;/Directory&gt;</code> inside of "<em>&lt;VirtualHost&gt;&lt;/VirtualHost&gt;</em>" tags.</p>
<p>Now, save the file (you need to have the administrative privileges), and restart your Apache server.</p>
<p>Done!</p>

<p>I hope this help will be useful and will save you much time.</p>
<p>P.S., Normally, all shared hosts are already configured to work with Apache mod_rewrite and allow configuration via .htaccess</p>
<p>&nbsp;</p>
<p>For checking if everything is OK and pH7CMS will be able to work, you can <a href="http://github.com/pH7Software/Rewrite-Mod-Test">use this test rewriting script</a>.</p>
