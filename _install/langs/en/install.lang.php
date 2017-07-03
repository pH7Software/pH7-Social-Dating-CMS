<?php
/**
 * @title            English Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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
    'requirements_desc' => 'WARNING! Please make sure <abbr title="Your Server. On loca host it will be you (your computer)">you are</abbr> connected to the Internet and your server has the <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">minimum requirements</a>.',
    'requirements2_desc' => 'Before to continue, please create a MySQL database and assign a user to it with full privileges. Once you created the MySQL database and its user, make sure to write down the database name, username and password since you will need them for installation.',
    'config_path' => '&quot;protected&quot; directory path',
    'desc_config_path' => 'Please specify the full path of your &quot;protected&quot; folder.<br />
        It is wise and advisable (but not mandatory in any case) to put this directory outside of the public directory of the Web server.',
    'need_frame' => 'You must use a Web browser that supports inline frames!',
    'path_protected' => 'Path of the &quot;protected&quot; folder',
    'next' => 'Next',
    'go' => 'Next Step =>',
    'later' => 'Not Now',
    'register' => 'Save It!',
    'site_name' => 'Site Name',
    'license' => 'Your License',
    'license_desc' => 'Please read the license carefully and accept it before continuing the installation of the software!',
    'registration_for_license' => 'If you haven\'t done it yet, it\'s a good time to buy now <a href="' . Controller::SOFTWARE_LICENSE_KEY_URL . '" target="_blank">a license key</a> to get all Professional Premium Features offered by the software.<br /> If you wish to try first pH7CMS with its basic features and promo links, you can skip this step.',
    'your_license' => 'Your License Key',
    'agree_license' => 'I have read and agree to the above Terms.',
    'step' => 'Step',
    'welcome' => 'Welcome to the installation of',
    'welcome_to_installer' => 'Installation of',
    'config_site' => 'Configure your website!',
    'config_system' => 'Database/System Configuration',
    'finish' => 'Congrats! The installation is finished and your site is alive!',
    'go_your_site' => 'Go to your new website!',
    'go_your_admin_panel' => 'Go to your admin panel!',
    'error_page_not_found' => 'Page not found',
    'error_page_not_found_desc' => 'Sorry, the page you are looking for could not be found.',
    'success_license' => 'Well done!',
    'failure_license' => 'Incorrect license format!',
    'no_protected_exist' => 'Sorry, we haven\'t found the &quot;protected&quot; directory.',
    'no_protected_readable' => 'Please change the permissions of the &quot;protected&quot; directory to read mode (CHMOD 755).',
    'no_public_writable' => 'Please change the permissions for the root public directory to write mode (CHMOD 777).',
    'no_app_config_writable' => 'Please change the permissions for &quot;protected/app/configs&quot; directory to write mode (CHMOD 777).',
    'database_error' => 'Error connecting to your database.<br />',
    'error_sql_import' => 'An error occurred while importing the file to your SQL database',
    'require_mysql_version' => 'You must install MySQL ' . PH7_REQUIRE_SQL_VERSION . ' or higher in order to continue.',
    'field_required' => 'This field is required',
    'all_fields_mandatory' => 'All fields marked with an asterisk (*) are required',
    'db_hostname' => 'Database server hostname',
    'desc_db_hostname' => 'Generally &quot;localhost&quot; or &quot;127.0.0.1&quot;',
    'db_name' => 'Database name',
    'db_username' => 'Database username',
    'db_password' => 'Database password',
    'db_prefix' => 'Prefix for tables in database',
    'desc_db_prefix' => 'This option is useful when you have multiple installations of pH7CMS on the same database.
        We recommend that you change the default values ​​in order to increase the security of your website.',
    'db_encoding' => 'Encoding',
    'desc_db_encoding' => 'Database encoding. Usually UTF8 encoding for international.',
    'db_port' => 'Database host port number',
    'desc_db_port' => 'Please leave to "3306" if you don\'t know.',
    'ffmpeg_path' => 'The path to the FFmpeg executable (if you don\'t know where it is, please ask your hosting)',
    'bug_report_email' => 'Bug reports email',
    'admin_first_name' => 'Your first name',
    'admin_last_name' => 'Your last name',
    'admin_username' => 'Your username (to login into the admin panel)',
    'admin_login_email' => 'Your email to login into the admin panel',
    'admin_email' => 'Admin email address for your site',
    'admin_return_email' => 'No-reply email address (usually noreply@yoursite.com)',
    'admin_feedback_email' => 'Email address for the contact form of your site',
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
    'content_email_finish_install' => '<p><strong>Congratulations, your website is now successfully installed!</strong></p>
        <p>We hope you\'ll enjoy using <em>' . Controller::SOFTWARE_NAME . '</em>!</p>
        <p>The URL of Your OWN Social/Dating website is: <em><a href="' . PH7_URL_ROOT . '">' . PH7_URL_ROOT . '</a></em></p>
        <p>Your Admin Panel URL is: <em><a href="' . PH7_URL_ROOT . PH7_ADMIN_MOD . '">' . PH7_URL_ROOT . PH7_ADMIN_MOD . '</a></em><br />
            Your Admin Login Email is: <em>' . (!empty($_SESSION['val']['admin_login_email']) ? $_SESSION['val']['admin_login_email'] : '') . '</em><br />
            Your Admin Login Username is: <em>' . (!empty($_SESSION['val']['admin_username']) ? $_SESSION['val']['admin_username'] : '') . '</em><br />
            Your Admin Login Password is: <em>****** (hidden for security reasons. It\'s the one you chose during the installation).</em>
        </p>
        <p>Don\'t forget to show off by showing YOUR new Social Dating Business to all your friends, your colleagues and your Facebook\'s mates (and even to your haters... or not :-) ).</p>
        <p>Finally, if you haven\'t done it yet, it\'s a really good time to buy a license today by simply <a href="' . Controller::SOFTWARE_LICENSE_KEY_URL . '" target="_blank">visiting our website</a> in order to get all Premium Modules/Features, Remove all Links and Copyright Notice on your Website and even get access to the Unlimited Support Ticket.</p>
        <p>&nbsp;</p>
        <p>P.S. For any bug reports, suggestions, partnership, translation, contribution or other,
        please visit our <a href="' . Controller::SOFTWARE_WEBSITE . '">website</a>.</p>
        <p>---</p>
        <p>Kind regards,</p>
        <p>The pH7CMS developers team.</p>',
    'yes_dir' => 'The directory was found successfully!',
    'no_dir' => 'The directory does not exist.',
    'wait_importing_database' => 'Please wait while importing the database.<br />
        This may take several minutes.',
    'service' => 'Useful additional services',
    'buy_copyright_license_title' => 'Buy a Copyright Removal License',
    'buy_copyright_license' => '<span class="gray">One-time Payment</span><br /> <span class="bold">Buy Now</span>',
    'buy_copyright_license_desc' => 'By buying a License, you won\'t have any Links and Copyright Notice on your site, you will get all Premium Mods/Features and also be entitled to all next Update/Upgrade versions of the software.',
    'buy_individual_ticket_support_title' => 'Buy an Individual Support Service',
    'buy_individual_ticket_support' => '<span class="gray">Full ticket support for one month</span><br /> <span class="bold">Buy Now</span>',
    'buy_individual_ticket_support_desc' => 'By purchasing an individual unlimited ticket support, we\'ll help you whenever you have an issue/problem with the software. We are at your total disposal to solve any problem encountered with pH7CMS.',
    'niche' => 'Choose the Kind of Site you Want to Build',
    'social_dating_niche' => 'Social-Dating Niche',
    'social_niche' => 'Community Niche',
    'dating_niche' => 'Dating Niche',
    'base_niche_desc' => 'By choosing this niche, the main modules will be enabled and the generic template (social dating community theme) will be enabled by default.',
    'zendate_niche_desc' => 'By choosing the Social niche, only Social modules will be enabled and the Social theme will be enabled by default.',
    'datelove_niche_desc' => 'By choosing the Dating niche, only Dating modules will be enabled on your site and the Dating theme will be enabled by default.',
    'go_social_dating' => 'Go for Social Dating!',
    'go_social' => 'Go for Social!',
    'go_dating' => 'Go for Dating!',
    'recommended' => 'Recommended',
    'recommended_desc' => 'Choose this niche if you haven\'t an idea yet.',
    'note_able_to_change_niche_settings_later' => 'Please note that you will be able to change the template and enable/disable the modules later in your admin panel.',
    'looking_hosting' => 'Looking for a Web host compatible with pH7CMS? See <a href="' . Controller::SOFTWARE_HOSTING_LIST_URL . '" target="_blank">our Web Hosting List</a>!',
    'warning_no_js' => 'JavaScript is disabled on your Web browser!<br />
        Please enable JavaScript via the options of your Web browser in order to use this website.',
    'admin_url' => 'Admin Panel URL',
    'powered' => 'Powered by',
    'loading' => 'Loading...',
);
