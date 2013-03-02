<h2>Les Cron Jobs sont vitale pour ce CMS.</h2>

<p>Les tâches Cron Jobs sont vitale pour ce CMS.</p>
<p>S'ils ne fonctionnent pas correctement, le site risque de ne pas fonctionner correctement et d'avoir des surcharge sur le serveur, ressource excessive en CPU, base de données surchargés, etc.</p>

<p>Les tâches Cron Jobs du CMS sont très faciles à utiliser.</p>
<p><strong>Attention, avant de continuer, vous devez changer le mot secret (c'est le paramètre "secret_word=" dans l'url du cron) du cron dans la partie d'administration du CMS par le votre pour éviter que d'autres personnes activent le cron à votre insu.</strong></p>

<p>Par exemple pour exécuter la tâche de la base de donnée du serveur MySQL,<br />
il suffit d'exécuter par exemple dans cPanel ou Plesk toutes les 96h avec cette url <pre>"GET http://VOTRE-SITE.COM/asset/ron/96h/Database/?secret_word=VOTRE_MOT_SECRET"</pre><br />
et facultatif avec une GET "option" et votre option, exemple <pre>"http://VOTRE-SITE.com/asset/ron/96h/Database/?secret_word=VOTRE_MOT_SECRET&option=repair"</pre> qui permet de réparer votre base de donnée dans le cas où votre base de donnée est détérioré ou encore réinitialiser les statistiques de votre site en passant comme paramètre "stat" à la place de "repair".

Faites ceci pour le reste des urls avec l'heure correspondent au nom du dossier.<br />

Toutes les tâches cron ce trouvent dans le répertoire suivant : <pre>"/CHEMIN-DE-VOTRE-SERVEUR/VOTRE-DOSSIER-PROTECTED/app/system/assets/cron/"</pre><br />

<strong>Attention, vous ne devez pas mettre la fin du fichier : "Cron.php" dans l'exécuteur des tâche cron.</strong></p>

<p>Si vous avez des difficultés avec la configuration des Crons Jobs, veuillez simplement acheter un ticket sur notre site (dans la section support), et nous allons le configurer correctement pour vous.</p>

<p><em>Un soutien professionnel est la meilleure solution pour un site internet !</em></p>
