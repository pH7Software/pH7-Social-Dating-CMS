<h2>Tasks Cron Jobs are vital to the CMS.</h2>

<p>If they do not work properly, the site may not function properly and have the server overload, excessive CPU resources, database overload, etc.</p>
<p>The tasks of the CMS Cron Jobs are very easy to use.</p>
<p><strong>Attention, before continuing, you must change the secret word (this is the parameter "secret_word=" in the URL of the cron) of cron in the administration by your CMS to prevent others activate the cron without your knowledge.</strong></p>

<p>For example, to perform the task of the database MySQL server,<br />
just run eg cPanel or Plesk every 96 hours with this url <pre>"GET http://YOUR-SITE.COM/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD"</pre> and optional with the option GET "option" and you option, example <pre>"http://YOUR-SITE.com/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD&option=repair"</pre> for repair your database or reset the statistics of your site by passing it as parameter "stat" instead of "repair".<br />

Do this for the rest of the urls with the time corresponding to the folder name.<br />

All cron jobs that are in the following directory: <pre>"/YOUR-PATH/YOUR-PROTECTED-FOLDER/app/system/assets/cron/"</pre><br />

<strong>Careful, you should not put the end of the file: Cron.php in the executor of the cron job.</strong></p>

<p>If you have problems with setting up cron jobs, simply buy a ticket on our site (in the support section) and we will configure it properly for you.</p>
<p><em>Professional support is the best solution for a website!</em></p>
