<?php
/**
 * @title            French Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / FR
 */

namespace PH7;

$LANG = array(
    'lang' => 'fr',
    'charset' => 'utf-8',
    'lang_name' => 'Français',
    'version' => 'version',
    'welcome_voice' => 'Bienvenue  sur l\'installation de ' . Controller::SOFTWARE_NAME . ', version ' . Controller::SOFTWARE_VERSION . '. ' .
        'J\'espère que tu vas aimer ton nouveau réseau de rencontre sociale.',
    'CMS_desc' => '<p>Bienvenue à l\'installation de ' . Controller::SOFTWARE_NAME . '.<br />
        Nous vous remercions d\'avoir choisis notre CMS et nous espérons qu\'il va vous plaire.</p>',
    'choose_install_lang' => 'Veuillez choisir votre langue pour commencer l\'installation',
    'requirements_desc' => 'ATTENTION ! Veuillez être sûr que <abbr title="Votre serveur distant ou votre machine/PC si vous êtes en localhost">vous êtes</abbr> connecté à Internet et que votre serveur a les <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">exigences nécessaires</a> pour faire fonctionner pH7CMS.',
    'requirements2_desc' => 'Avant toute chose, veuillez créer une base de données MySQL et affecter un utilisateur à elle avec tous les privilèges. Une fois que vous avez créé la base de données MySQL et son utilisateur, assurez-vous d\'écrire le nom de la base de données, le nom d\'utilisateur et le mot de passe, car vous en aurez besoin pour l\'installation.',
    'config_path' => 'Chemin du répertoire &laquo; protected &raquo;',
    'desc_config_path' => 'Veuillez indiquer le chemin complet de votre répertoire &laquo; protected &raquo;<br />
        Il est préférable et conseillé (mais en aucun cas pas obligatoire) de le mettre ce répertoire en dehors de la racine publique de votre site Web.',
    'need_frame' => 'Vous devez utiliser un navigateur Web qui accepte les iframes !',
    'path_protected' => 'Chemin du répertoire &laquo; protected &raquo;',
    'next' => 'Suivant',
    'go' => 'Étape Suivante =>',
    'later' => 'Pas maintenant',
    'register' => 'Enregistrer !',
    'site_name' => 'Nom de votre site',
    'license' => 'Votre Licence',
    'license_desc' => 'Veuillez lire la licence attentivement et l\'accepter avant de continuer l\'installation du logiciel !',
    'registration_for_license' => 'Si vous ne l\'avez pas encore fait, c\'est un excellent moment pour acheter <a href="' . Controller::SOFTWARE_LICENSE_KEY_URL . '" target="_blank">une licence</a> afin d\'obtenir les Modules Pro offerts par le logiciel.<br /> Si votre voulez essayer la version d\'essai et contenant les liens promotionnels, vous pouvez sauter cette étape.',
    'your_license' => 'Votre clé de licence',
    'agree_license' => 'J\'ai lu et j\'accepte les Termes de licence ci-dessus.',
    'step' => 'Étape',
    'welcome' => 'Bienvenue sur l\'installation de',
    'welcome_to_installer' => 'Installation de',
    'config_site' => 'Configurer votre site !',
    'config_system' => 'Configuration du système du CMS !',
    'finish' => 'Installation terminée !',
    'go_your_site' => 'Aller sur votre site !',
    'go_your_admin_panel' => 'Allez à votre panneau d\'administration !',
    'error_page_not_found' => 'Page introuvable',
    'error_page_not_found_desc' => 'Désolé, mais la page que vous cherchez est introuvable.',
    'success_license' => 'Bien joué !',
    'failure_license' => 'Le format de la licence est incorrecte !',
    'no_protected_exist' => 'Désolé, mais nous n\'avons pas trouvé le répertoire &laquo; protected &raquo;',
    'no_protected_readable' => 'Veuillez changer les permissions du répertoire &laquo; protected &raquo; pour qu\'il soit en mode &laquo; lecture &raquo; (CHMOD 755).',
    'no_public_writable' => 'Veuillez changer les permissions du répertoire &laquo; public &raquo; pour qu\'il soit en mode &laquo; écriture &raquo; (CHMOD 777).',
    'no_app_config_writable' => 'Veuillez changer les permissions du répertoire &laquo; protected/app/configs &raquo; pour qu\'il soit en mode &laquo; écriture &raquo; (CHMOD 777).',
    'database_error' => 'Erreur de connexion avec votre base de données.<br />',
    'error_sql_import' => 'Une erreur s\'est produit pendant l\'importation de du fichier SQL vers votre base de données',
    'require_mysql_version' => 'Vous devez installer MySQL ' . PH7_REQUIRE_SQL_VERSION . ' ou supérieur afin de pouvoir continuer.',
    'field_required' => 'Ce champ est obligatoire',
    'all_fields_mandatory' => 'Tous les champs marqués d\'un astérisque (*) sont obligatoires',
    'db_hostname' => 'Nom de l\'hôte du serveur de la base de données',
    'desc_db_hostname' => '(Très souvent cette valeur est &quot;localhost&quot; ou &quot;127.0.0.1&quot;)',
    'db_name' => 'Nom de la base de données',
    'db_username' => 'Nom d\'utilisateur de la base de données',
    'db_password' => 'Mot de passe de la base de données',
    'db_prefix' => 'Le préfixe des tables de la base de données',
    'desc_db_prefix' => 'Cette option est utile quand vous avez plusieurs installations de pH7CMS sur la même base de données. Nous vous recommandons quand même de modifier les valeurs par défaut afin d\'augmenter la sécurité de votre site Web.',
    'db_encoding' => 'Encodage',
    'desc_db_encoding' => 'Encodage de la base de données. Généralement UTF8 pour un encodage internationale.',
    'db_port' => 'Numéro de port de votre base de données',
    'desc_db_port' => 'Veuillez laisser la valeur à "3306" si vous ne savez pas.',
    'ffmpeg_path' => 'Le chemin vers l\'exécutable FFmpeg (si vous ne le savez pas où il se trouve, veuillez vous renseigner auprès de votre hébergeur)',
    'bug_report_email' => 'E-mail de rapport de bogues',
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
        <p>Nous espérons que vous allez avoir beaucoup de plaisir avec <em>' . Controller::SOFTWARE_NAME . '</em> !</p>
        <p>L\'URL de VOTRE Site de Rencontre est : <em><a href="' . PH7_URL_ROOT . '">' . PH7_URL_ROOT . '</a></em></p>
        <p>L\'URL du panneau d\'administration est : <em><a href="' . PH7_URL_ROOT . PH7_ADMIN_MOD . '">' . PH7_URL_ROOT . PH7_ADMIN_MOD . '</a></em><br />
            Votre adresse e-mail pour le panneau d\'administration est : <em>' . (!empty($_SESSION['val']['admin_login_email']) ? $_SESSION['val']['admin_login_email'] : '') . '</em><br />
            Votre nom d\'utilisateur pour le panneau d\'administration est : <em>' . (!empty($_SESSION['val']['admin_username']) ? $_SESSION['val']['admin_username'] : '') . '</em><br />
            Votre mot de passe est : <em>****** (caché pour des raisons de sécurité. C\'est celui choisi durant l\'installation).</em>
        </p>
        <p><strong>N\'oubliez pas de vous la péter en montrant votre nouveau service de rencontre à tous vos amis, vos collègues et vos potes de Facebook</strong> (et même à vos ennemis... ou pas).</p>
        <p>Enfin, si cela n\'est pas encore fait, c\'est une excellente idée pour acheter aujourd\'hui une clé de licence en vous rendant simplement sur <a href="' . Controller::SOFTWARE_LICENSE_KEY_URL . '" target="_blank">notre site Web</a> afin d\'obtenir les fonctionnalités Premium, la suppression des liens et les mentions de copyright sur votre site et même l\'accès au support de ticket illimité.</p>
        <p>&nbsp;</p>
        <p>Pour tous rapport de bogues, suggestions, partenariat, participation au développement du logiciel et/ou à sa traduction, etc.,
        veuillez visiter notre <a href="' . Controller::SOFTWARE_WEBSITE . '">site Web</a>.</p>
        <p>---</p>
        <p>Cordialement,</p>
        <p>L\'équipe de développement de pH7CMS.</p>',
    'yes_dir' => 'Le répertoire a été trouvé avec succès !',
    'no_dir' => 'Le répertoire n\'existe pas.',
    'wait_importing_database' => 'Veuillez patienter pendant l\'importation de la base de donnée.<br />
        Cette opération peut prendre plusieurs minutes.',
    'service' => 'Services additionnels utiles',
    'buy_copyright_license_title' => 'Acheter une licence',
    'buy_copyright_license' => '<span class="gray">Licence à vie</span><br /> <span class="bold">Acheter maintenant</span>',
    'buy_copyright_license_desc' => 'En achetant une licence, vous n\'allez plus avoir de liens promotionnels et mentions de droit d\'auteur sur votre site, obtenir les tous modules pro et vous serez également capable de mettre à jour/à niveau le logiciel.',
    'buy_individual_ticket_support_title' => 'Acheter un service support technique individuel',
    'buy_individual_ticket_support' => '<span class="gray">Support technique individuel complet pour un mois</span><br /> <span class="bold">Acheter maintenant</span>',
    'buy_individual_ticket_support_desc' => 'En achetant un support technique individuel, nous allons vous aider à chaque fois que vous aurez un problème avec notre logiciel. Nous serons à votre disposition pour résoudre d\'éventuelles problème que rencontreriez avec pH7CMS.',
    'niche' => 'Choisissez le type de site que vous voulez avoir ',
    'social_dating_niche' => 'Niche de Rencontre Sociale',
    'social_niche' => 'Niche de Réseautage Sociale',
    'dating_niche' => 'Niche Rencontre',
    'base_niche_desc' => 'En choisissant cette niche, tous les modules seront activés et le thème générique (rencontre/portail social) sera activé par défaut.',
    'zendate_niche_desc' => 'En choisissant cette niche, uniquement les modules sociaux seront activés et le thème social sera activé par défaut.',
    'datelove_niche_desc' => 'En choisissant cette niche, uniquement les modules &laquo; Rencontre &raquo; seront activés et le thème Rencontre sera activé par défaut.',
    'go_social_dating' => 'Rencontre Sociale',
    'go_social' => 'Portail Social',
    'go_dating' => 'Rencontre',
    'recommended' => 'Recommandée',
    'recommended_desc' => 'Choisissez cette niche si vous ne savez pas quelle niche choisir',
    'note_able_to_change_niche_settings_later' => 'Veuillez noter que vous pourrez changer le thème et activer/désactiver les modules par la suite dans votre panneau d\'administration.',
    'looking_hosting' => 'À la recherche d\'un hébergement Web ? Regardez <a href="' . Controller::SOFTWARE_HOSTING_LIST_FR_URL . '" target="_blank">notre Liste d\'Hébergeurs</a> ?',
    'warning_no_js' => 'Cette page Web est incompatible sans l\'activation de JavaScript.<br />
        Veuillez activer JavaScript via les options de votre navigateur Web.',
    'admin_url' => 'URL du panneau d\'administration',
    'powered' => 'Propulsé par',
    'loading' => 'Chargement en cours...',
);
