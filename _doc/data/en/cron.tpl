<h2>Cron Jobs</h2>

<p>Cron Jobs are very important for pH7CMS.</p>
<p>If they do not work properly, the site may not function properly and have the server overload, excessive CPU resources, database overload, etc.</p>
<p>The pH7CMS crons are very easy to configure.</p>
<p><strong>Attention, before continuing, you must change the secret word of cron (this is the parameter "secret_word" in the cron URL) by yours in the administration panel to prevent others activate the cron without your knowledge.</strong></p>

<p>For example, to perform the task of the database MySQL server,<br />
 you just need run it (e.g., cPanel or Plesk) every 96 hours with this URL <pre>"GET http://YOUR-SITE.COM/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD"</pre><br />
You also have GET "option" parameters that are optional. Example: <pre>"http://YOUR-SITE.com/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD&option=repair"</pre> for repair your database or reset the statistics of your site by passing it as parameter "stat" instead of "repair".<br />

Do this for the rest of the URLs with the time corresponding to the folder name.<br />

All cron jobs are located in the following directory: <pre>"/YOUR-PATH/YOUR-PROTECTED-FOLDER/app/system/assets/cron/"</pre><br />

<strong>Careful, you should not put the end of the file: Cron.php in the executor of the cron job.</strong></p>