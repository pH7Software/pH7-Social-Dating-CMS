<h2>Installing the software in just 5 steps!</h2>

<h3>1. Extract the pH7CMS archive</h3>
<p>Extract the Zip archive using a file archiver software such as <a href="http://www.7-zip.org/download.html">7-Zip</a>.</p>

<h3>2. Upload files to your server</h3>
<p>Using an FTP client such as <a href="https://filezilla-project.org/download.php?type=client" title="FileZilla Client">FileZilla</a> or cPanel File Manager.<br />
You must transfer ALL files and folders (even license files and empty folders and files).</p>

<h3>3. For optimal security</h3>
<p>Rename the "_protected" folder or move it outside the root of your server.</p>

<h3>4. File permissions (optional)</h3>
<p>If your OS server is a Unix-like, please check if the file permissions (CHMOD) are correctly configured.<br />
In numerical values that must be <em>755</em> for all folders and <em>644</em> for all files.<br />
Warning, the following folders must have <em>777</em> permissions:</p>
<pre>~/YOUR-PUBLIC-FOLDER/data/system/modules/*</pre>
<pre>~/YOUR-PUBLIC-FOLDER/_install/*</pre>
<pre>~/YOUR-PUBLIC-FOLDER/_repository/module/*</pre>
<pre>~/YOUR-PUBLIC-FOLDER/_repository/upgrade/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/app/configs/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/data/cache/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/data/backup/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/data/tmp/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/data/log/*</pre>
<pre>~/YOUR-PROTECTED-FOLDER/framework/Core/*</pre>
<p><span class="warning">Warning, these permissions don't allow editing and creating files in the File Management admin module.</span><br />
If you want to allow it, you need to set in numerical values <em>777</em> for all folders and <em>666</em> for all files.</p>

<h3>5. Run the installation wizard</h3>
<p>Go to your website URL, <em>http://YOUR-WEBSITE.com/<strong>_install</strong>/</em> and follow the instructions carefully.</p>

<p><iframe height="400" src="//www.youtube.com/embed/fFSI-AEMQwY?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe></p>
