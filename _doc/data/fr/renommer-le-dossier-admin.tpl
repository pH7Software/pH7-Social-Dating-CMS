<h2>Comment renommer le dossier "admin123"</h2>

<p>Pour garantir une excellente sécurité et protéger l'administration de votre site, nous vous conseillons de renommer le dossier "admin123".</p>
<p>Pour ce faire, renommer le dossier par le nom de votre choix, puis éditer le fichier <em>~/VOTRE_DOSSIER_PROTÉGÉ/app/configs/constants.php</em> et modifier</p>
<pre><code class="php">define ( 'PH7_ADMIN_MOD', 'admin123' );</code></pre>
<p>par le nouveau nom du dossier d'administration.</p>
<p>et voilà c'est déjà fini ! 😉</p>
