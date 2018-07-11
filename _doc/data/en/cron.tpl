<h2>Cron Jobs</h2>

<p>Cron Jobs are important for pH7CMS.</p>
<p>If they do not work properly, the site may not function properly and have the server overload, excessive CPU resources, database overload, etc.</p>
<p>The crons in pH7CMS are very easy to configure.</p>
<p>
    <strong>Attention, before continuing, you must change the secret word of cron (this is the parameter "secret_word" in the cron URL) by yours (through the <i>Admin Panel -> Settings -> General -> Automation</i>) to prevent others activate the cron without your knowledge.</strong>
</p>

<p>
    For example, to perform the task for the database MySQL server, <br />
    you just need run it (e.g., cPanel or Plesk) every 96 hours with this URL <code>"GET http://YOUR-SITE.COM/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD"</code><br />
    You also have GET "option" parameters that are optional. Example: <code>"http://YOUR-SITE.com/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD&option=repair"</code> for repair your database or reset the statistics of your site by passing it as parameter "stat" instead of "repair".
</p>

<p><strong>An example below with crontab:</strong></p>
<pre>
 crontab -e
 0 0 */4 * *  "GET http://YOUR-SITE.COM/asset/cron/96h/Database/?secret_word=YOUR_SECRET_WORD"
</pre>

<p>
    Do this for the rest of the URLs with the time corresponding to the folder name.<br />
    All cron jobs are located in the following directory: <code>"/YOUR-PATH/YOUR-PROTECTED-FOLDER/app/system/core/assets/cron/"</code><br />
    <strong>Be Careful! Don't specify the end of the file: Cron.php in the executor of the cron job.</strong>
</p>
