<?php
/**
 * @title            French Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / FR
 */

namespace PH7;

$LANG = array(
    'lang' => 'fr',
    'charset' => 'utf-8',
    'lang_name' => 'Français',
    'version' => 'version',
    'welcome_voice' => 'Bienvenue sur l\'installation de ' . Controller::SOFTWARE_NAME . ', version ' . Controller::SOFTWARE_VERSION . '. ' .
        'J\'espère que tu vas aimer ton nouveau réseau de rencontre sociale.',
    'CMS_desc' => '<p>Bienvenue à l\'installation de ' . Controller::SOFTWARE_NAME . '.<br />
        Nous vous remercions d\'avoir choisis notre CMS et nous espérons qu\'il va vous plaire.</p>',
    'choose_install_lang' => 'Veuillez choisir votre langue pour commencer l\'installation',
    'requirements_desc' => 'ATTENTION ! Veuillez être sûr que <abbr title="Votre serveur distant ou votre machine/PC si vous êtes en localhost">vous êtes</abbr> connecté à Internet et que votre serveur a les <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank" rel="noopener">exigences nécessaires</a> pour faire fonctionner pH7CMS.',
    'requirements2_desc' => 'Avant toute chose, veuillez créer une base de données MySQL et affecter un utilisateur à elle avec tous les privilèges. Une fois que vous avez créé la base de données MySQL et son utilisateur, assurez-vous d\'écrire le nom de la base de données, le nom d\'utilisateur et le mot de passe, car vous en aurez besoin pour l\'installation.',
    'config_path' => 'Chemin du répertoire &laquo; protected &raquo;',
    'desc_config_path' => 'Veuillez indiquer le chemin complet de votre répertoire &laquo; protected &raquo;<br />
        Il est préférable et conseillé (mais en aucun cas pas obligatoire) de le mettre ce répertoire en dehors de la racine publique de votre site Web.',
    'need_frame' => 'Vous devez utiliser un navigateur Web qui accepte les iframes !',
    'path_protected' => 'Chemin du répertoire &laquo; protected &raquo;',
    'next' => 'Suivant',
    'go' => 'Étape Suivante =>',
    'later' => 'Pas maintenant',
    'license_agreements' => 'Licence et Accords',
    'license_agreements_desc' => 'Veuillez lire la licence et les accords attentivement et de les accepter avant de continuer l\'installation du logiciel.',
    'register' => 'Enregistrer !',
    'site_name' => 'Nom de votre site',
    'agree_license' => 'J\'ai lu et j\'accepte les Termes de licence ci-dessus.',
    'conform_to_laws' => 'J\'accepte de toujours garder mon site web entièrement légal et de me conformer à toutes lois et régulations applicables susceptibles de s\'appliquer à moi, à mon entreprise, à mon site web et ses utilisateurs, et de vérifier et <a href="https://ph7cms.com/doc/en/how-to-edit-the-static-and-legal-pages" target="_blank" rel="noopener">mettre à jour les "CGU", "charte de confidentialité", "mentions légales" (et toutes autres pages légales nécessaires)</a> afin de me conformer pleinement aux lois et régulations applicables.',
    'responsibilities_agreement' => 'J\'accepte d\'utiliser ce logiciel à mes risques et périls et que l\'auteur de ce logiciel ne pourra en aucun cas être tenu responsable des dommages-intérêts directs ou indirects, ni de tout autre dommage de quelque nature que ce soit, résultant de l\'utilisation de ce logiciel ou de l\'impossibilité d\'utiliser le logiciel pour quelque raison que ce soit.',
    'step' => 'Étape',
    'welcome' => 'Bienvenue sur l\'installation de',
    'welcome_to_installer' => 'Installation de',
    'config_site' => 'Configurer votre site !',
    'config_system' => 'Configuration du système du CMS !',
    'finish' => '🏆 Installation terminée ! 🤗',
    'go_your_site' => 'Aller sur votre site !',
    'go_your_admin_panel' => 'Allez à votre panneau d\'administration',
    'error_page_not_found' => 'Page introuvable',
    'error_page_not_found_desc' => 'Désolé, mais la page que vous cherchez est introuvable.',
    'no_protected_exist' => 'Désolé, mais nous n\'avons pas trouvé le répertoire &laquo; protected &raquo;',
    'no_protected_readable' => 'Veuillez changer les permissions du répertoire &laquo; protected &raquo; pour qu\'il soit en mode &laquo; lecture &raquo; (CHMOD 755).',
    'no_public_writable' => 'Veuillez changer les permissions du répertoire &laquo; racine &raquo; pour qu\'il soit en mode &laquo; écriture &raquo; (CHMOD 777).',
    'no_app_config_writable' => 'Veuillez changer les permissions du répertoire &laquo; protected/app/configs &raquo; pour qu\'il soit en mode &laquo; écriture &raquo; (CHMOD 777).',
    'database_error' => 'Erreur de connexion avec votre base de données.<br />',
    'error_sql_import' => 'Une erreur s\'est produit pendant l\'importation de du fichier SQL vers votre base de données',
    'require_mysql_version' => 'Vous devez installer MySQL ' . PH7_REQUIRED_SQL_VERSION . ' ou supérieur afin de pouvoir continuer.',
    'field_required' => 'Ce champ est obligatoire',
    'all_fields_mandatory' => 'Tous les champs marqués d\'un astérisque (*) sont obligatoires',
    'db_hostname' => 'Nom de l\'hôte du serveur de la base de données',
    'desc_db_hostname' => 'Très souvent cette valeur est &quot;localhost&quot; ou &quot;127.0.0.1&quot;',
    'db_name' => 'Nom de la base de données',
    'db_username' => 'Nom d\'utilisateur de la base de données',
    'db_password' => 'Mot de passe de la base de données',
    'db_prefix' => 'Le préfixe des tables de la base de données',
    'desc_db_prefix' => 'Cette option est utile quand vous avez plusieurs installations de pH7CMS sur la même base de données. Nous vous recommandons quand même de modifier la valeur par défaut afin d\'augmenter la sécurité de votre site Web.',
    'db_encoding' => 'Encodage',
    'desc_db_encoding' => 'Encodage de la base de données. utf8mb4 pour un encodage international (supportant les emojis).',
    'db_port' => 'Numéro de port de votre base de données',
    'desc_db_port' => 'Veuillez laisser la valeur à "3306" si vous ne savez pas.',
    'ffmpeg_path' => 'Le chemin vers l\'exécutable FFmpeg (si vous ne le savez pas où il se trouve, veuillez vous renseigner auprès de votre hébergeur)',
    'bug_report_email' => 'E-mail de rapport de bogues',
    'bug_report_email_placeholder' => 'bug@nom-de-domaine.com',
    'admin_first_name' => 'Votre prénom',
    'admin_last_name' => 'Votre nom de famille',
    'admin_username' => 'Votre nom d\'utilisateur pour vous connecter au panneau d\'administration de votre site',
    'admin_login_email' => 'Votre adresse e-mail pour vous connecter au panneau d\'administration de votre site',
    'admin_email' => 'L\'adresse e-mail d\'administration',
    'admin_return_email' => 'Adresse e-mail sans réponse (généralement noreply@votre-site.com)',
    'admin_feedback_email' => 'L\'adresse e-mail pour le formulaire de contact (feedback)',
    'admin_password' => 'Votre mot de passe',
    'admin_passwords' => 'Veuillez confirmer votre mot de passe',
    'bad_email' => 'E-mail incorrecte',
    'bad_username' => 'Votre pseudo est incorrect',
    'username_too_short' => 'Votre pseudo est trop court, minimum 3 caractères',
    'username_too_long' => 'Votre pseudo est trop long, maximum 30 caractères',
    'password_no_number' => 'Votre mot de passe doit contenir au moins un chiffre',
    'password_no_upper' => 'Votre mot de passe doit contenir au moins une majuscule',
    'password_too_short' => 'Votre mot de passe est trop court. 6 caractères minimum',
    'password_too_long' => 'Votre mot de passe est trop long',
    'passwords_different' => 'Le mot de passe de confirmation ne correspond pas au mot de passe initial',
    'bad_first_name' => 'Veuillez entrer votre prénom, il doit également être compris entre 2 et 20 caractères.',
    'bad_last_name' => 'Veuillez entrer votre prénom, il doit également être compris entre 2 et 20 caractères.',
    'insecure_password' => 'Pour votre sécurité, votre mot de passe doit être différent de vos informations personnelles (pseudo, prénom et nom de famille).',
    'remove_install_folder' => 'Veuillez supprimer le dossier &laquo; _install &raquo; de votre serveur avant d\'utiliser votre site.',
    'remove_install_folder_auto' => 'Effacer automatiquement le répertoire &laquo; install &raquo; (cette opération nécessite les droits d\'accès nécessaires sur le dossier &laquo; install &raquo;).',
    'confirm_remove_install_folder_auto' => 'ATTENTION, tous les fichiers du dossiers /_install/ vont être supprimés.',
    'title_email_finish_install' => 'À propos de l\'installation : Informations',
    'content_email_finish_install' => '<p><strong>Félicitations, votre site Web est maintenant installé avec succès !</strong></p>
        <p>J\'espère que vous allez avoir beaucoup de plaisir avec <em>' . Controller::SOFTWARE_NAME . '</em> !</p>
        <p>L\'URL de VOTRE Site de Rencontre est : <em><a href="' . PH7_URL_ROOT . '">' . PH7_URL_ROOT . '</a></em></p>
        <p>L\'URL du panneau d\'administration est : <em><a href="' . PH7_URL_ROOT . PH7_ADMIN_MOD . '">' . PH7_URL_ROOT . PH7_ADMIN_MOD . '</a></em><br />
            Votre adresse e-mail pour le panneau d\'administration est : <em>' . (!empty($_SESSION['val']['admin_login_email']) ? $_SESSION['val']['admin_login_email'] : '') . '</em><br />
            Votre nom d\'utilisateur pour le panneau d\'administration est : <em>' . (!empty($_SESSION['val']['admin_username']) ? $_SESSION['val']['admin_username'] : '') . '</em><br />
            Votre mot de passe est : <em>****** (caché pour des raisons de sécurité. C\'est celui choisi durant l\'installation).</em>
        </p>
        <p>N\'oubliez pas de vous la péter en montrant votre nouveau service de rencontre à tous vos amis, vos collègues et vos potes de Facebook (et même à vos ennemis... ou pas).</p>
        <p><strong>Voici un <a href="' . get_tweet_post("Viens de créer mon #AppDeRencontre avec #pH7CMS 😍 %s \n%s #ScriptRencontre 💪", Controller::SOFTWARE_TWITTER, Controller::SOFTWARE_GIT_REPO_URL) . '">un Tweet pré-écrit</a> (que vous pouvez éditer, bien sûr)</strong>.</p>
        <p>&nbsp;</p>
        <p><strong>Allez-vous m\'aider à améliorer le logiciel ? <a href="' . Controller::PATREON_URL . '">Faire une donation ici</a></strong></p>
        <p>&nbsp;</p>
        <p>Pour tout rapport de bogues, suggestions, partenariat, participation au développement du logiciel et/ou à sa traduction, etc.,
        veuillez visiter le <a href="' . Controller::SOFTWARE_GIT_REPO_URL . '">dépôt GitHub</a>.</p>
        <p>---</p>
        <p>Bien à vous,<br />
        <strong><a href="' . Controller::AUTHOR_URL . '">Pierre Soria</a></strong></p>',
    'yes_dir' => 'Le répertoire a été trouvé avec succès !',
    'no_dir' => 'Le répertoire n\'existe pas.',
    'wait_importing_database' => 'Veuillez patienter pendant l\'importation de la base de donnée.<br />
        Cette opération peut prendre plusieurs minutes.',
    'add_sample_data' => 'Générer des profils d\'exemple (vous pouvez les supprimer par la suite)',
    'niche' => 'Choisissez le type de site que vous voulez avoir 😇',
    'social_dating_niche' => 'Niche de Rencontre Sociale',
    'social_niche' => 'Niche de Réseautage Sociale',
    'dating_niche' => 'Niche Rencontre',
    'base_niche_desc' => 'En choisissant cette niche, tous les modules seront activés et le thème générique (rencontre/portail social) sera activé par défaut.',
    'zendate_niche_desc' => 'En choisissant cette niche, uniquement les modules sociaux seront activés et le thème social sera activé par défaut.',
    'datelove_niche_desc' => 'En choisissant cette niche, uniquement les modules &laquo; Rencontre &raquo; seront activés et le thème Rencontre sera activé par défaut.',
    'go_social_dating' => 'Rencontre Sociale',
    'go_social' => 'Niche Portail Social',
    'go_dating' => 'Niche Rencontre',
    'recommended' => 'Niche recommandée',
    'recommended_desc' => 'Choisissez cette niche si vous ne savez pas quelle niche choisir',
    'note_able_to_change_niche_settings_later' => 'Veuillez noter que vous pourrez changer le thème et activer/désactiver les modules par la suite dans votre panneau d\'administration.',
    'will_you_make_donation' => 'Allez-vous m\'aider à améliorer le logiciel ?',
    'donate_here' => 'Faire une donation maintenant 💪',
    'or_paypal_donation' => 'et/ou le faire via PayPal ! 🤩',
    'warning_no_js' => 'Cette page Web est incompatible sans l\'activation de JavaScript.<br />
        Veuillez activer JavaScript via les options de votre navigateur Web.',
    'admin_url' => 'URL du panneau d\'administration',
    'powered' => 'Propulsé par',
    'loading' => 'Chargement en cours...',
);
