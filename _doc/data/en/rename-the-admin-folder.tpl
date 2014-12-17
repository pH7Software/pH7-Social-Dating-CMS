<h2>How to rename the "admin123" folder</h2>

<p>To ensure excellent safety and protect the administration of your site, we recommend that you rename the "admin123" folder.</p>
<p>To do this, rename the folder with the name of your choice, then edit the file <em>~/YOUR_PROTECTED_FOLDER/app/configs/constants.php</em> and change</p>
<pre>define ( 'PH7_ADMIN_MOD', 'admin123' );</pre>
<p>by the new admin folder name.</ p>
<p>Done! It's really easy! ;-)</ p>
