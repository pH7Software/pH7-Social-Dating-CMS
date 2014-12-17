<h2>Mise à niveau de logiciel</h2>


<h3>Extraire l'archive de mise à jour</h3>
<p>Décompressez l'archive ZIP de mise à jour en utilisant un logiciel d'archivage de fichiers comme <a href="http://sourceforge.net/projects/sevenzip/">7-Zip</a>.</p>

<h3>Téléchargez les fichiers vers votre serveur</h3>
<p>Utilisez un client FTP comme <a href="http://filezilla-project.org" title="FileZilla Client">FileZilla</a>.<br />
Transférer TOUS les fichiers et dossiers (même les dossiers et les fichiers vides). Ne supprimez pas les anciens fichiers, vous devez simplement les remplacer.</p>

<h3>Permissions des fichiers</h3>
<p>Si le système d'exploitation de votre serveur est de type Unix, vous devez vérifier si les permissions des fichiers (CHMOD) sont correctement configuré.<br />
En valeurs numériques cela doit être <em>755</em> pour tous les dossiers et <em>644</em> pour tous les fichiers.<br />
Attention, les dossiers suivants doivent avoir les permissions <em>777</em> :</p>
<pre>~/VOTRE-DOSSIER-PUBLIC/</pre>
<pre>~/VOTRE-DOSSIER-PUBLIC/_install/*</pre>
<pre>~/VOTRE-DOSSIER-PUBLIC/_repository/module/*</pre>
<pre>~/VOTRE-DOSSIER-PUBLIC/_repository/upgrade/*</pre>
<pre>~/VOTRE-DOSSIER-PROTECTED/app/configs/*</pre>
<pre>~/VOTRE-DOSSIER-PROTECTED/data/cache/*</pre>
<pre>~/VOTRE-DOSSIER-PROTECTED/data/backup/*</pre>
<pre>~/VOTRE-DOSSIER-PROTECTED/data/tmp/*</pre>
<pre>~/VOTRE-DOSSIER-PROTECTED/data/log/*</pre>
<p><span class="warning">Attention, ces permissions ne permettant pas l'édition et la création de fichiers dans le module File Management du panneau d'administration.</span><br />
Si vous voulez le permettre, vous devez mettre en valeurs numériques <em>777</em> pour tous les dossiers et <em>666</em> pour tous les fichiers.</p>

<h3>Lancez la mise à jour</h3>
<p>Vous devez vous connecter en tant qu'administrateur et vous rendre à cette URL suivante et suivre attentivement les instructions : <em>http://VOTRE-SITE.COM/<strong>asset/file/Upgrade</strong></em></p>

<h3>Videz les caches</h3>
<p>Vider tous les caches par le biais de votre panneau d'administration.</p>
