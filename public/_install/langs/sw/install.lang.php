<?php
/**
 * @title            Swahili Language File
 *
 * @author           Jones B. <support@csshood.com>
 * @copyright        (c) 2013, Jones B. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / SW
 * @update           08/26/13 00:35
 */

namespace PH7;

$LANG = array (
    'lang' => 'sw',
    'charset' => 'utf-8',
    'lang_name' => 'Kiswahili',
    'version' => 'version',
    'CMS_desc' => '<p>Karibu katika ufungaji wa '.Controller::SOFTWARE_NAME.'.<br />
    Asante kwa kuchagua programu yetu. Tunatumai utaridhika</p>
    <p>Tafadhali fuata maelekezo yetu sita ya ufungaji.</p>',
    'chose_lang_for_install' => 'Tafadhali chagua lugha utakayoitumia katika ufungaji',
    'requirements_desc' => 'ONYO! Hakikisha mashine yako inaridhisha <a href="'.Controller::SOFTWARE_REQUIREMENTS_URL.'" target="_blank">matakwa</a> yafuatyo ili ufunge vyema pH7CMS.',
    'config_path' => 'Eneo la saraka &quot;iliyodhibitiwa&quot;',
    'desc_config_path' => 'Tafadhali onyesha eneo la saraka iliyodhibitiwa.<br />
    Unashauriwa kutia saraka hii nje ya &quot;public root&quot; ya tovuti yako.',
    'need_frame' => 'Unshauriwa kutumia brausa inayohimili &quot;inline frames&quot;!',
    'path_protected' => 'Eneo la &quot;saraka iliyodhibitiwa&quot;',
    'next' => 'ifuatayo',
    'go' => 'Hatua ifuatayo =>',
    'license' => 'Leseni',
    'license_desc' => 'Tafadhali soma leseni vyema kisha ukubali kabla ya kuendelea kupanga PH7!',
    'registration_for_license' => 'Tafadhali jiandikishe kwa <a href="'.Controller::SOFTWARE_WEBSITE.'" target="_blank">wavuti</a> huu ili upate leseni yako ya bure.',
    'your_license' => 'Leseni Yako',
    'agree_license' => 'Nimesoma na nikakubali matakwa ya leseni.',
    'step' => 'Hatua',
    'welcome' => 'Kariba katika upangaji wa',
    'welcome_to_installer' => 'Upangaji wa',
    'config_site' => 'Konfiga wavuti wako!',
    'config_system' => 'Konfiga mfumo huu!',
    'bad_email' => 'Barua pepe sio sahihi',
    'finish' => 'Maliza Upangaji!',
    'go_your_site' => 'Enda kwa wavuti wako mpya!',
    'error_page_not_found' => 'Ukurasa Haujapatikana',
    'error_page_not_found_desc' => 'Pole, ukurasa unaoutafuta haujapatikana.',
    'success_license' => 'Leseni yako ni sahihi!',
    'failure_license' => 'Pole, leseni yako sio sahihi!',
    'no_protected_exist' => 'Pole, lakini hatujapata saraka yako iliyodhibitiwa.',
    'no_protected_readable' => 'Tafadhili badilisha ruhusa ya saraka yako iliyodhibitiwa (CHMOD 755).',
    'no_public_writable' => 'Tafadhali badilisha ruhusa ya &quot;public&quot; saraka yako (CHMOD 777).',
    'no_app_config_writable' => 'Tafadhali badilisha ruhusa ya &quot;protected/app/configs&quot; saraka yako (CHMOD 777).',
    'database_error' => 'Kosa! Hatuwezi kuunganisha &quot;database&quot; yako.<br />',
    'error_sql_import' => 'Kosa lilitendeka wakati wa kuagiza faili kwa &quot;SQL database&quot; yako',
    'field_required' => 'Lazima uandike kwa nafasi hii',
    'all_fields_mandatory' => 'Nafasi zote zenye alama (*) zahitajika',
    'db_hostname' => '&quot;Database Server hostname&quot;',
    'desc_db_hostname' => '(Kwa ujumla &quot;localhost&quot; or &quot;127.0.0.1&quot;)',
    'db_name' =>'Jina la &quot;database&quot;',
    'db_username' => 'Jina la mtumiaji wa &quot;database&quot;',
    'db_password' => 'Maneno ya siri ya &quot;Database&quot;',
    'db_prefix' => 'Kiambishi cha table ya database',
    'desc_db_prefix' => 'Chaguo hili ni muhimu ikiwa una upangaji zaidi ya moja wa pH7CMS. Tunakushauri ubadilishe maneno haya ili kuongeza usalama wa wavuti wako.',
    'desc_charset' => 'Database Encoding, kwa kawaida ni UTF8 encoding.',
    'db_port' => 'Port ya database',
    'ffmpeg_path' => 'Eneo la FFmpeg executable (kama haujui iliko, tafadhali uliza anayekupatia wavuti)',
    'password_empty' => 'Jina ama nambari yako ya siri haijatiwa',
    'passwordS_different' => 'Jina ama nambari yako ya siri haifanani na ile uliyotoa awali',
    'username_badusername' => 'Jina lako la utumizi sio halali',
    'username_tooshort' => 'Jina lako la utumizi ni fupi, kwa chini zaidi ni alama 4',
    'username_toolong' => 'Jina lako la utumizi ni refu, kwa zaidi ni alama 40',
    'email_empty' => 'Lazima ujaze barua pepe',
    'password_nonumber' => 'Jina ama nambari yako ya siri yapaswa kuwa na angalau nambari moja',
    'password_noupper' => 'Jina ama nambari yako ya siri lazima iwe na angalua herufi kubwa moja',
    'password_tooshort' => 'Jina ama nambari yako ya siri ni fupi sana',
    'password_toolong' => 'Jina ama nambari yako ya siri ni ndefu sana',
    'bug_report_email' => 'Barua pepe ya kuripoti shida katika wavuti',
    'admin_first_name' => 'Jina la Kwanza',
    'admin_last_name' => 'Jina la Mwisho',
    'admin_username' => 'Jina lako la matumizi ili kuingia katika utawala wa wavuti wako',
    'admin_login_email' => 'Barua yako ya pepe ili kuingia katika utawala wa wavuti wako',
    'admin_email' => 'Barua yako ya pepe ya utawala',
    'admin_return_email' => 'Barua yako ya pepe amabyo haitakubali majibu(kwa ujumla huwa noreply@wavutiwako.com)',
    'admin_feedback_email' => 'Barua yako ya pepe itakayotumika katika mawasiliano na wageni wa wavuti wako (maoni)',
    'admin_password' => 'Jina ama nambari yako ya Siri',
    'admin_passwordS' => 'Tafadhali dhibitisha jina ama nambari yako ya siri',
    'bad_first_name' => 'Tafadhali andika Jina lako la kwanza, lazima liwe kati ya herufi 2 na 20.',
    'bad_last_name'=> 'Tafadhali andika Jina lako la mwisho, lazima liwe kati ya herufi 2 na 20.',
    'remove_install_folder_auto' => 'Toa saraka ya &quot;install&quot; (hii inahitaji uwe na ruhusa ya kutoa saraka).',
    'confirm_remove_install_folder_auto' => 'ONYO, faili zote katika saraka ya /_install/ zitatolewa.',
    'title_email_finish_install' => 'Pongezi, upangaji wa wavuti wako umekamilika!',
    'content_email_finish_install' => '<p><strong>Pongezi, wavuti wako umepangwa vyema!</strong></p>
    <p>Tunaimani utafurahia kutumia '.Controller::SOFTWARE_NAME.'</p>
    <p>Kwa shida zozote za kimatumizi, maombi, maoni ama ushirikiano</p>
    <p>Tafadhali tembelea <a href="'.Controller::SOFTWARE_WEBSITE.'" target="_blank">wavuti</a> wetu.</p>
    <p>---</p>
    <p>Ni mimi wako msharifu,</p>
    <p>The pH7CMS developers team.</p>',
    'yes_dir' => 'Saraka imepatikana vyema!',
    'no_dir' => 'Saraka haijapatikana.',
    'wait_importing_database' => 'Tafadhali subiri...<br />
    Yaweza chukua muda.',
    'error_get_server_url' => 'Shida ya mapatano na anayekutimizia wavuti.<br />
    Tafadhali hakikisha kuwa una wavuti (internet).',
    'powered' => 'Imetolewa na',
    'loading' => 'Subiri...',
);
