<h2>How to set the development mode</h2>

<p class="italic"><span class="warning">WARNING, this procedure concerns only pH7CMS versions lower than 1.3.0</span> Since pH7CMS 1.3, this option is available in the admin panel -> Tools -> Environment Mode.</p>

<p>To set the development mode, please edit <code>~/_YOUR-PROTECTED-FOLDER/app/configs/config.ini</code><br />
Replace <code>"environment = production"</code> by <code>"environment = development"</code> at the beginning of the file and then save it.<br />
<span class="warning">ATTENTION</span>, if your OS server is a Unix-like (Linux, Mac OS, ...), you have to change the permission "<em>644</em>" to "<em>666</em>" in order to be able to save it.<br /><br />
After that, it is also better to clear your browser cache (CTRL+F5).</p>
