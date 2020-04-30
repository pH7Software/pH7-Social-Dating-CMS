<?php
/**
 * @title            English Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / EN
 */

namespace PH7;

$LANG = array(
    'lang' => 'en',
    'charset' => 'utf-8',
    'lang_name' => 'English',
    'version' => 'version',
    'welcome_voice' => 'Welcome to ' . Controller::SOFTWARE_NAME . ', version ' . Controller::SOFTWARE_VERSION . '. ' .
        'I hope you will enjoy your new social web app.',
    'CMS_desc' => '<p>Welcome to ' . Controller::SOFTWARE_NAME . ' Installer.<br />
        Thank you for choosing <strong>pH7CMS</strong>, and we hope you will love it!</p>',
    'choose_install_lang' => 'Please choose your language to begin the installation',
    'requirements_desc' => 'WARNING! Please make sure <abbr title="Your Server. On local host, it will be you (your computer)">you are</abbr> connected to the Internet and your server has the <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank" rel="noopener">minimum requirements</a>.',
    'requirements2_desc' => 'Before to continue, please create a MySQL database and assign a user to it with full privileges. Once you created the MySQL database and its user, make sure to write down the database name, username and password since you will need them for installation.',
    'config_path' => '&quot;protected&quot; directory path',
    'desc_config_path' => 'Please specify the full path of your &quot;protected&quot; folder.<br />
        It is wise and advisable (but not mandatory in any case) to put this directory outside of the public directory of the Web server.',
    'need_frame' => 'You must use a Web browser that supports inline frames!',
    'path_protected' => 'Path of the &quot;protected&quot; folder',
    'next' => 'Next',
    'go' => 'Next Step =>',
    'later' => 'Not Now',
    'license_agreements' => 'License and Agreements',
    'license_agreements_desc' => 'Please read the license and agreements carefully and accept them before continuing the installation.',
    'register' => 'Save It!',
    'site_name' => 'Site Name',
    'agree_license' => 'I have <strong>read</strong> and <strong>agree</strong> to the above Terms.',
    'conform_to_laws' => 'I <strong>agree to always keep my website fully legal and to conform with any applicable laws and regulations</strong> that may apply to me, to my corporation, to my website and its users, and to review and <a href="https://ph7cms.com/doc/en/how-to-edit-the-static-and-legal-pages" target="_blank" rel="noopener">update the "TOS", "Privacy Policy" (and any other required legal pages of my website)</a> in order to fully comply with the applicable laws and regulations.',
    'responsibilities_agreement' => 'I <strong>agree to use the software at my own risk</strong> and that the author of this software cannot in any case be held liable for direct or indirect damage, nor for any other damage of any kind whatsoever, resulting from the use of this software or the impossibility to use it for any reason whatsoever.',
    'step' => 'Step',
    'welcome' => 'Welcome to the installation of',
    'welcome_to_installer' => 'Installation of',
    'config_site' => 'Configure your website!',
    'config_system' => 'Database/System Configuration',
    'finish' => 'Woohoo! 🚀 pH7CMS is now installed! 😋',
    'go_your_site' => 'Go to your new website!',
    'go_your_admin_panel' => 'Go to your admin panel',
    'error_page_not_found' => 'Page not found',
    'error_page_not_found_desc' => 'Sorry, the page you are looking for could not be found.',
    'no_protected_exist' => 'Sorry, we haven\'t found the &quot;protected&quot; directory.',
    'no_protected_readable' => 'Please change the permissions of the &quot;protected&quot; directory to read mode (CHMOD 755).',
    'no_public_writable' => 'Please change the permissions of the root public directory to write mode (CHMOD 777).',
    'no_app_config_writable' => 'Please change the permissions for &quot;protected/app/configs&quot; directory to write mode (CHMOD 777).',
    'database_error' => 'Error connecting to your database.<br />',
    'error_sql_import' => 'An error occurred while importing the file to your SQL database',
    'require_mysql_version' => 'You must install MySQL ' . PH7_REQUIRED_SQL_VERSION . ' or higher in order to continue.',
    'field_required' => 'This field is required',
    'all_fields_mandatory' => 'All fields marked with an asterisk (*) are required',
    'db_hostname' => 'Database server hostname',
    'desc_db_hostname' => 'Usually &quot;localhost&quot; or &quot;127.0.0.1&quot;',
    'db_name' => 'Database name',
    'db_username' => 'Database username',
    'db_password' => 'Database password',
    'db_prefix' => 'Table name prefix',
    'desc_db_prefix' => 'This option is useful when you have multiple installations of pH7CMS on the same database.
        We also recommend that you change the default value ​​in order to increase the security of your website.',
    'db_encoding' => 'Encoding',
    'desc_db_encoding' => 'Database encoding. Leave utf8mb4 for international encoding, including emojis.',
    'db_port' => 'Database host port number',
    'desc_db_port' => 'Leave it to "3306" if you don\'t know.',
    'ffmpeg_path' => 'The path to the FFmpeg executable (if you don\'t know where it is, please ask your hosting company)',
    'bug_report_email' => 'Bug reports email',
    'bug_report_email_placeholder' => 'error_log@yourdomain.com',
    'admin_first_name' => 'Your first name',
    'admin_last_name' => 'Your last name',
    'admin_username' => 'Your username (to login into the admin panel)',
    'admin_login_email' => 'Email to login into the admin panel',
    'admin_email' => 'Admin email address for your website',
    'admin_return_email' => 'No-reply email address (usually noreply@yoursite.com)',
    'admin_feedback_email' => 'Email address for the contact form of your website',
    'admin_password' => 'Your password (to login into the admin panel)',
    'admin_passwords' => 'Please confirm your password',
    'bad_email' => 'Incorrect email',
    'bad_username' => 'Your username is incorrect',
    'username_too_short' => 'Your username is too short, at least 3 characters',
    'username_too_long' => 'Your username is too long, maximum 30 characters',
    'password_no_number' => 'Your password must contain at least one number',
    'password_no_upper' => 'Your password must contain at least one uppercase letter',
    'password_too_short' => 'Your password is too short. Must be at least 6 characters',
    'password_too_long' => 'Your password is too long',
    'passwords_different' => 'The confirmation password doesn\'t match with the initial one',
    'bad_first_name' => 'Please enter your first name, it must also be between 2 and 20 characters.',
    'bad_last_name' => 'Please enter your last name, it must also be between 2 and 20 characters.',
    'insecure_password' => 'For your security, your password must be different than your personal information (username, first and last name).',
    'remove_install_folder' => 'For security reasons, please remove the &quot;_install&quot; folder from your server before using your website.',
    'remove_install_folder_auto' => 'Automatically delete the &quot;install&quot; directory (this requires access rights to delete the &quot;install&quot; directory).',
    'confirm_remove_install_folder_auto' => 'WARNING, All files in the /_install/ folder will be removed.',
    'title_email_finish_install' => 'About your installation: Information',
    'content_email_finish_install' => '<p><strong>Congratulations! Your website is now successfully installed!</strong></p>
        <p>I hope you\'ll enjoy <em>' . Controller::SOFTWARE_NAME . '</em> a lot!</p>
        <p>The URL of Your OWN Social/Dating website is: <em><a href="' . PH7_URL_ROOT . '">' . PH7_URL_ROOT . '</a></em></p>
        <p>Your Admin Panel URL is: <em><a href="' . PH7_URL_ROOT . PH7_ADMIN_MOD . '">' . PH7_URL_ROOT . PH7_ADMIN_MOD . '</a></em><br />
            Your Admin Login Email is: <em>' . (!empty($_SESSION['val']['admin_login_email']) ? $_SESSION['val']['admin_login_email'] : '') . '</em><br />
            Your Admin Login Username is: <em>' . (!empty($_SESSION['val']['admin_username']) ? $_SESSION['val']['admin_username'] : '') . '</em><br />
            Your Admin Login Password is: <em>****** (hidden for security reasons. It\'s the one you chose during the installation).</em>
        </p>
        <p>Don\'t forget to show off YOUR new Social Dating Website to your friends, colleagues and Facebook\'s mates (and even to your haters... why not! :-) ).</p>
        <p><strong>Here is a <a href="' . get_tweet_post("Built my #Social #DatingWebsite with #pH7CMS 😍 %s \n%s #DatingSoftware 🚀", Controller::SOFTWARE_TWITTER, Controller::SOFTWARE_GIT_REPO_URL) . '">pre-written Tweet</a> (which you can edit, of course)</strong>.</p>
        <p>&nbsp;</p>
        <p><strong>Will you help me to improve the software..? <a href="' . Controller::PATREON_URL . '">Make a donation here</a></strong></p>
        <p>&nbsp;</p>
        <p>P.S. For any bug reports, suggestions, partnership, translation, contribution or other,
        please visit the <a href="' . Controller::SOFTWARE_GIT_REPO_URL . '">GitHub Repo</a>.</p>
        <p>---</p>
        <p>Kind regards,<br />
        <strong><a href="' . Controller::AUTHOR_URL . '">Pierre Soria</a></strong></p>',
    'yes_dir' => 'The directory was found successfully!',
    'no_dir' => 'The directory does not exist.',
    'wait_importing_database' => 'Please wait while importing the database.<br />
        This may take several minutes.',
    'add_sample_data' => 'Generate sample profiles (you will be able to remove them later on)',
    'niche' => 'Choose the Kind of WebApp you Want to Build 😇',
    'social_dating_niche' => 'Social-Dating Niche 🥰',
    'social_niche' => 'Community Niche 🥳',
    'dating_niche' => 'Dating Niche 😍',
    'base_niche_desc' => 'By choosing this niche, the main modules will be enabled and the generic template (social dating community theme) will be chosen by default.',
    'zendate_niche_desc' => 'By choosing the Social niche, only the Social modules will be enabled, profile photo won\'t be required by default and the Social theme will be the default one.',
    'datelove_niche_desc' => 'By choosing the Dating niche, only the Dating modules will be enabled on your website, profile photo will be required by default and the Dating theme will be the default one.',
    'go_social_dating' => 'Go for Social Dating!',
    'go_social' => 'Go for Social!',
    'go_dating' => 'Go for Dating!',
    'recommended' => 'Recommended Niche',
    'recommended_desc' => 'Choose this niche if you haven\'t an idea yet.',
    'note_able_to_change_niche_settings_later' => 'Please note that you will be able to change the template and enable/disable the modules later in your admin panel.',
    'will_you_make_donation' => '😇 Will you help me to maintain &amp; improve the software?',
    'donate_here' => 'Subscribe now to be a patron 🏆',
    'or_paypal_donation' => '💸 And/Or, you can also contribute through PayPal 🤩',
    'warning_no_js' => 'JavaScript is disabled on your Web browser!<br />
        Please enable JavaScript via the options of your Web browser in order to use this website.',
    'admin_url' => 'Admin Panel URL',
    'powered' => 'Proudly powered by',
    'loading' => 'Loading...',
);
