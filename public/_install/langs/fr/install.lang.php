<?php
/**
 * @title            French Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / FR
 * @update           09/10/13 23:06
 */

namespace PH7;

$LANG = array (
    'lang' => 'fr',
    'charset' => 'utf-8',
    'lang_name' => 'Français',
    'version' => 'version',
    'CMS_desc' => '<p>Bienvenue à l\'installation de ' . Controller::SOFTWARE_NAME . '.<br />
    Nous vous remercions d\'avoir choisis notre CMS et nous espérons qu\'il va vous plaire.</p>
    <p>Veuillez suivre attentivement les sept étapes de l\'installation.</p>',
    'choose_install_lang' => 'Veuillez choisir votre langue pour commencer l\'installation',
    'requirements_desc' => 'ATTENTION ! Veuillez être sûr que votre serveur a les <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">exigences</a> nécessaires pour faire correctement fonctionner pH7CMS.',
    'config_path' => 'Chemin du répertoire &laquo; protected &raquo;',
    'desc_config_path' => 'Veuillez indiquer le chemin complet de votre répertoire &laquo; protected &raquo;<br />
    Il est préférable et conseillé de le mettre ce répertoire en dehors de la racine publique de votre site Web.',
    'need_frame' => 'Vous devez utiliser un navigateur Web qui accepte les iframes !',
    'path_protected' => 'Chemin du répertoire &laquo; protected &raquo;',
    'next' => 'Suivant',
    'go' => 'Étape suivante =>',
    'license' => 'Licence',
    'license_desc' => 'Veuillez lire la licence attentivement et l\'accepter avant de continuer l\'installation du logiciel !',
    'registration_for_license' => 'Veuillez vous inscrire sur <a href="' . Controller::SOFTWARE_REGISTRATION_URL . '" target="_blank">cette page</a> afin d\'obtenir gratuitement votre clé de licence qui est requise pour pouvoir continuer.',
    'your_license' => 'Votre clé de licence',
    'agree_license' => 'J\'ai lu et j\'accepte les Termes de licence ci-dessus.',
    'step' => 'Étape',
    'welcome' => 'Bienvenue sur l\'installation de',
    'welcome_to_installer' => 'Installation de',
    'config_site' => 'Configurer votre site !',
    'config_system' => 'Configuration du système du CMS !',
    'bad_email' => 'E-mail incorrecte',
    'finish' => 'Installation terminée !',
    'go_your_site' => 'Aller sur votre site !',
    'go_your_admin_panel' => 'Allez à votre panneau d\'administration !',
    'error_page_not_found' => 'Page introuvable',
    'error_page_not_found_desc' => 'Désolé, mais la page que vous cherchez est introuvable.',
    'success_license' => 'Votre clé de licence est correcte !',
    'failure_license' => 'Désolé, votre clé de licence est incorrecte !',
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
    'db_name' =>'Nom de la base de données',
    'db_username' => 'Nom d\'utilisateur de la base de données',
    'db_password' => 'Mot de passe de la base de données',
    'db_prefix' => 'Le préfixe des tables de la base de données',
    'desc_db_prefix' => 'Cette option est utile quand vous avez plusieurs installations de pH7CMS sur la même base de données. Nous vous recommandons quand même de modifier les valeurs par défaut afin d\'augmenter la sécurité de votre site Web.',
    'db_encoding' => 'Encodage',
    'desc_db_encoding' => 'Encodage de la base de données. Généralement UTF8 pour un encodage internationale.',
    'db_port' => 'Numéro de port de votre base de données',
    'ffmpeg_path' => 'Le chemin vers l\'exécutable FFmpeg (si vous ne le savez pas où il se trouve, veuillez vous renseigner auprès de votre hébergeur)',
    'passwords_different' => 'Le mot de passe de confirmation ne correspond pas au mot de passe initial',
    'username_bad_username' => 'Votre pseudo est incorrect',
    'username_too_short' => 'Votre pseudo est trop court, minimum 4 caractères',
    'username_too_long' => 'Votre pseudo est trop long, maximum 40 caractères',
    'password_no_number' => 'Votre mot de passe doit contenir au moins un chiffre',
    'password_no_upper' => 'Votre mot de passe doit contenir au moins une majuscule',
    'password_too_short' => 'Votre mot de passe est trop court',
    'password_too_long' => 'Votre mot de passe est trop long',
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
    'bad_first_name' => 'Veuillez entrer votre prénom, il doit également être compris entre 2 et 20 caractères.',
    'bad_last_name' => 'Veuillez entrer votre prénom, il doit également être compris entre 2 et 20 caractères.',
    'remove_install_folder_auto' => 'Effacer automatiquement le répertoire &laquo; install &raquo; (cette opération nécessite les droits d\'accès nécessaires sur le dossier &laquo; install &raquo;).',
    'confirm_remove_install_folder_auto' => 'ATTENTION, tous les fichiers du dossiers /_install/ vont être supprimés.',
    'remove_install_folder' => 'Veuillez supprimer le dossier &laquo; _install &raquo; de votre serveur avant d\'utiliser votre site.',
    'title_email_finish_install' => 'Félicitation, l\'installation de votre site Web est terminé !',
    'content_email_finish_install' => '<p><strong>Félicitations, votre site Web est maintenant installé avec succès !</strong></p>
    <p>Nous espérons que vous allez avoir beaucoup de plaisir avec <em>' . Controller::SOFTWARE_NAME . '</em> !</p>
    <p>Pour tous rapport de bogues, suggestions, propositions, partenariat, participation au développement du CMS et à sa traduction, etc.,
    veuillez visitez notre <a href="' . Controller::SOFTWARE_WEBSITE . '" target="_blank">site Web</a>.</p>
    <p>---</p>
    <p>Cordialement,</p>
    <p>L\'équipe de développement de pH7CMS.</p>',
    'yes_dir' => 'Le répertoire a été trouvé avec succès !',
    'no_dir' => 'Le répertoire n\'existe pas.',
    'wait_importing_database' => 'Veuillez patienter pendant l\'importation de la base de donnée.<br />
    Cette opération peut prendre plusieurs minutes.',
    'service' => 'Services additionnels utiles',
    'buy_copyright_license_title' => 'Acheter une licence',
    'buy_copyright_license' => '<span class="bold italic">320£</span> <span class="gray">Licence à vie</span> <span class="right">Acheter maintenant</span>',
    'buy_copyright_license_desc' => 'En achetant une clé de licence, vous n\'aurez plus aucuns liens et mentions de droit d\'auteur sur votre site.',
    'buy_individual_ticket_support_title' => 'Acheter un service support technique individuel',
    'buy_individual_ticket_support' => '<span class="bold italic">55£</span> <span class="gray">Support technique individuel complet pour un mois</span> <span class="right">Acheter maintenant</span>',
    'buy_individual_ticket_support_desc' => 'En achetant un support technique individuel, nous allons vous aider à chaque fois que vous aurez un problème avec notre logiciel. Nous serons à votre disposition pour résoudre d\'éventuelles problème que rencontreriez avec pH7CMS.',
    'looking_hosting' =>'À la recherche d\'un hébergement Web ? Regardez <a href="' . Controller::SOFTWARE_HOSTING_LIST_URL . '" target="_blank">notre liste des hébergements</a> ?',
    'error_get_server_url' => 'Problème d\'accès avec notre serveur Web.<br />
    Veuillez vérifier que votre serveur est bien connecté à internet, sinon veuillez un peu patienté (il est possible que notre serveur est surchargé).',
    'powered' => 'Propulsé par',
    'loading' => 'Chargement en cours...',
);
