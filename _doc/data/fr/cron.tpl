<h2>Les Cron Jobs</h2>

<p>Les tâches Cron Jobs sont très importants pour pH7CMS.</p>
<p>S'ils ne fonctionnent pas correctement, le site risque de ne pas fonctionner correctement et d'avoir des surcharge sur le serveur, ressource excessive en CPU, base de données surchargés, etc.</p>

<p>L'utilisation des tâches périodiques de pH7CMS sont très faciles à configurer.</p>
<p><strong>Attention, avant de continuer, vous devez changer le mot secret du cron (c'est le paramètre "secret_word" dans l'URL du cron) par le vôtre dans la partie d'administration afin d'éviter que d'autres personnes activent le cron à votre insu.</strong></p>

<p>Par exemple pour exécuter la tâche de la base de données du serveur MySQL,<br />
 il suffit de l'exécuter (par exemple dans cPanel ou Plesk) toutes les 96 heures avec cette URL <code>"GET http://VOTRE-SITE.COM/asset/cron/96h/Database/?secret_word=VOTRE_MOT_SECRET"</code><br />
Vous avez aussi des paramètres GET "option" qui sont facultatifs. Exemple : <code>"http://VOTRE-SITE.com/asset/cron/96h/Database/?secret_word=VOTRE_MOT_SECRET&option=repair"</code> qui permet de réparer votre base de données dans le cas où votre base de données est détérioré ou encore réinitialiser les statistiques de votre site en passant comme paramètre "stat" à la place de "repair".

Faites ceci pour le reste des URLs avec l'heure correspondent au nom du dossier.<br />

Toutes les tâches cron se trouvent dans le répertoire suivant : <code>"/CHEMIN-DU-SERVEUR/VOTRE-DOSSIER-PROTECTED/app/system/core/assets/cron/"</code><br />

<strong>Attention, vous ne devez pas mettre la fin du fichier : "Cron.php" dans l'exécuteur des tâches cron.</strong></p>
