<h2>Why I can't directly submit a form?</h2>

<p>Sometime, when you want to submit a form, you might receive the following error message:</p>
<pre><em>The security token does not exist or the time of expiry of the security token has expire. Please try again!</em></pre>
<p>This is a security message that prevents the form from being sent directly because pH7CMS uses tokens to prevent <abbr title="Cross-Site Request Forgery">CSRF</abbr> vulnerabilities.</p>
<p>Generally, you get it when you stay too long on the same form, because there is a time limit. However, you can change this time in the <span class="underline">Admin Panel -> Settings -> General -> Security -> CSRF token lifetime</span>.</p>
