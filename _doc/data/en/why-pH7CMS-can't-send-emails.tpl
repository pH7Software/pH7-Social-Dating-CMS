<h2>Why pH7CMS can't send emails?</h2>

<p>Sometime on production hosts and almost always on local hosts, you may encounter the following error message when pH7CMS has to send emails:</p>
<pre><em>Oops! Our email server encountered an internal error and the email could not be sent. Please try again later!</em></pre>
<p>This means there is an incorrect configuration on your host side and/or with the "php.ini" file.</p>
<p>In general, we recommend checking your SMTP configuration and your "php.ini" config file. If you still get the error, you should contact your hosting company to fix the problem for sending emails with PHP.</p>
<p>Also, be aware that some hosting companies don't allow sending emails for spam reasons.</p>
<p>Finally, we always recommend using <a href="http://ph7cms.com/doc/en/hosting" title="List of recommended Web Hosting for pH7CMS">a host recommended by us</a>.</p>

<p>&nbsp;</p>
<p>P.S. To prevent your emails from being marked as spam, you can use a transactional email service such as <a href="https://sendgrid.com">SendGrid</a> or <a href="https://www.mailgun.com">Mailgun</a>.</p>
