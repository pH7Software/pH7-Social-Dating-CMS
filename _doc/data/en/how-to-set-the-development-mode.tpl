<h2>How to set the development mode</h2>

<p class="italic">
    <span class="warning">WARNING, this procedure concerns only pH7CMS versions lower than 1.3.0</span><br />
    From pH7CMS 1.3, this option is available in the Admin Panel -> Tools -> Environment Mode.<br />
    <span class="underline">However, you might still need to set the development mode manually. For example, if you are not able to login into the admin panel at that time or if the error occurs all pages of the website.</span>
</p>


<p>To set the development mode, please edit <code>~/_YOUR-PROTECTED-FOLDER/app/configs/config.ini</code><br />
Replace "<code>environment = production</code>" to "<code>environment = development</code>" at the beginning of the file and then save it.<br />
<span class="warning">ATTENTION</span>, if your OS server is a Unix-like (Linux, Mac OS, ...), you have to change the permission file from "<em>644</em>" to "<em>666</em>" in order to be able to save it.<br /><br />
After that, it is also better to clear your browser cache (CTRL+F5).</p>
