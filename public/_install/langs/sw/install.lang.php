<?php
/**
 * @title            Swahili Language File
 *
 * @author           Jones B. <support@csshood.com>
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013, Jones B. All Rights Reserved.
 * @copyright        (c) 2013-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / SW
 * @update           09/10/13 23:06
 */

namespace PH7;

$LANG = array (
    'lang' => 'sw',
    'charset' => 'utf-8',
    'lang_name' => 'Kiswahili',
    'version' => 'version',
    'CMS_desc' => '<p>Karibu katika ufungaji wa ' . Controller::SOFTWARE_NAME . '.<br />
    Asante kwa kuchagua programu yetu. Tunatumai utaridhika</p>
    <p>Tafadhali fuata maelekezo yetu saba ya ufungaji.</p>',
    'choose_install_lang' => 'Tafadhali chagua lugha utakayoitumia katika ufungaji',
    'requirements_desc' => 'ONYO! Hakikisha mashine yako inaridhisha <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">matakwa</a> yafuatyo ili ufunge vyema pH7CMS.',
    'config_path' => 'Eneo la saraka &quot;protected&quot;',
    'desc_config_path' => 'Tafadhali onyesha eneo la saraka &quot;protected&quot;.<br />
    Ni busara na vyema kuweka saraka hii nje mzizi wa umma wa mtandao kompyuta yako.',
    'need_frame' => 'Unshauriwa kutumia brausa inayohimili &quot;inline frames&quot;!',
    'path_protected' => 'Eneo la saraka &quot;protected&quot;',
    'next' => 'ifuatayo',
    'go' => 'Hatua ifuatayo =>',
    'license' => 'Leseni',
    'license_desc' => 'Tafadhali soma leseni vyema kisha ukubali kabla ya kuendelea kupanga PH7!',
    'registration_for_license' => 'Tafadhali jiandikishe kwa <a href="' . Controller::SOFTWARE_REGISTRATION_URL . '" target="_blank">ukurasa</a> huu ili upate leseni yako ya bure.',
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
    'go_your_admin_panel' => 'Kwenda admin yako jopo!',
    'error_page_not_found' => 'Ukurasa Haujapatikana',
    'error_page_not_found_desc' => 'Pole, ukurasa unaoutafuta haujapatikana.',
    'success_license' => 'Leseni yako ni sahihi!',
    'failure_license' => 'Pole, leseni yako sio sahihi!',
    'no_protected_exist' => 'Pole, lakini hatujapata saraka yako &quot;protected&quot;.',
    'no_protected_readable' => 'Tafadhili badilisha ruhusa ya saraka yako &quot;protected&quot; (CHMOD 755).',
    'no_public_writable' => 'Tafadhali badilisha ruhusa ya &quot;public&quot; saraka yako (CHMOD 777).',
    'no_app_config_writable' => 'Tafadhali badilisha ruhusa ya &quot;protected/app/configs&quot; saraka yako (CHMOD 777).',
    'database_error' => 'Kosa! Hatuwezi kuunganisha &quot;database&quot; yako.<br />',
    'error_sql_import' => 'Kosa lilitendeka wakati wa kuagiza faili kwa &quot;SQL database&quot; yako',
    'require_mysql_version' => 'Lazima kufunga MySQL ' . PH7_REQUIRE_SQL_VERSION . ' au juu zaidi ili kuendelea.',
    'field_required' => 'Lazima uandike kwa nafasi hii',
    'all_fields_mandatory' => 'Nafasi zote zenye alama (*) zahitajika',
    'db_hostname' => '&quot;Database Server hostname&quot;',
    'desc_db_hostname' => '(Kwa ujumla &quot;localhost&quot; or &quot;127.0.0.1&quot;)',
    'db_name' =>'Jina la &quot;database&quot;',
    'db_username' => 'Jina la mtumiaji wa &quot;database&quot;',
    'db_password' => 'Maneno ya siri ya &quot;database&quot;',
    'db_prefix' => 'Kiambishi cha table ya &quot;database&quot;',
    'desc_db_prefix' => 'Chaguo hili ni muhimu ikiwa una upangaji zaidi ya moja wa pH7CMS. Tunakushauri ubadilishe maneno haya ili kuongeza usalama wa wavuti wako.',
    'db_encoding' => 'Encoding',
    'desc_db_encoding' => 'Database Encoding, kwa kawaida ni UTF8 encoding.',
    'db_port' => 'Port ya &quot;database&quot;',
    'ffmpeg_path' => 'Eneo la FFmpeg executable (kama haujui iliko, tafadhali uliza anayekupatia wavuti)',
    'passwords_different' => 'Jina ama nambari yako ya siri haifanani na ile uliyotoa awali',
    'username_bad_username' => 'Jina lako la utumizi sio halali',
    'username_too_short' => 'Jina lako la utumizi ni fupi, kwa chini zaidi ni alama 4',
    'username_too_long' => 'Jina lako la utumizi ni refu, kwa zaidi ni alama 40',
    'password_no_number' => 'Jina ama nambari yako ya siri yapaswa kuwa na angalau nambari moja',
    'password_no_upper' => 'Jina ama nambari yako ya siri lazima iwe na angalua herufi kubwa moja',
    'password_too_short' => 'Jina ama nambari yako ya siri ni fupi sana',
    'password_too_long' => 'Jina ama nambari yako ya siri ni ndefu sana',
    'bug_report_email' => 'Barua pepe ya kuripoti shida katika wavuti',
    'admin_first_name' => 'Jina la Kwanza',
    'admin_last_name' => 'Jina la Mwisho',
    'admin_username' => 'Jina lako la matumizi ili kuingia katika utawala wa wavuti wako',
    'admin_login_email' => 'Barua yako ya pepe ili kuingia katika utawala wa wavuti wako',
    'admin_email' => 'Barua yako ya pepe ya utawala',
    'admin_return_email' => 'Barua yako ya pepe amabyo haitakubali majibu(kwa ujumla huwa noreply@wavutiwako.com)',
    'admin_feedback_email' => 'Barua yako ya pepe itakayotumika katika mawasiliano na wageni wa wavuti wako (maoni)',
    'admin_password' => 'Jina ama nambari yako ya Siri',
    'admin_passwords' => 'Tafadhali dhibitisha jina ama nambari yako ya siri',
    'bad_first_name' => 'Tafadhali andika Jina lako la kwanza, lazima liwe kati ya herufi 2 na 20.',
    'bad_last_name'=> 'Tafadhali andika Jina lako la mwisho, lazima liwe kati ya herufi 2 na 20.',
    'remove_install_folder_auto' => 'Toa saraka ya &quot;install&quot; (hii inahitaji uwe na ruhusa ya kutoa saraka).',
    'remove_install_folder' => 'Tafadhali kuondoa "_install" folder kutoka server yako kabla ya kutumia tovuti yako.',
    'confirm_remove_install_folder_auto' => 'ONYO, faili zote katika saraka ya /_install/ zitatolewa.',
    'title_email_finish_install' => 'Pongezi, upangaji wa wavuti wako umekamilika!',
    'content_email_finish_install' => '<p><strong>Pongezi, wavuti wako umepangwa vyema!</strong></p>
    <p>Tunaimani utafurahia kutumia <em>' . Controller::SOFTWARE_NAME . '</em></p>
    <p>Kwa shida zozote za kimatumizi, maombi, maoni ama ushirikiano,
    tafadhali tembelea <a href="' . Controller::SOFTWARE_WEBSITE . '" target="_blank">wavuti</a> wetu.</p>
    <p>---</p>
    <p>Ni mimi wako msharifu,</p>
    <p>The pH7CMS developers team.</p>',
    'yes_dir' => 'Saraka imepatikana vyema!',
    'no_dir' => 'Saraka haijapatikana.',
    'wait_importing_database' => 'Tafadhali subiri...<br />
    Yaweza chukua muda.',
    'service' => 'Huduma muhimu ya ziada',
    'buy_copyright_license_title' => 'Kununua leseni',
    'buy_copyright_license' => '<span class="bold italic">£320</span> <span class="gray">Mara moja</span> <span class="right">Kununua sasa</span>',
    'buy_copyright_license_desc' => 'By kununua muhimu leseni, huwezi kuwa na viungo yoyote na hati miliki matangazo kwenye tovuti yako.',
    'buy_individual_ticket_support_title' => 'Kununua msaada wa huduma ya mtu binafsi',
    'buy_individual_ticket_support' => '<span class="bold italic">£55</span> <span class="gray">Tiketi ya msaada kamili kwa mwezi mmoja</span> <span class="right">Kununua sasa</span>',
    'buy_individual_ticket_support_desc' => 'By ununuzi wa mtu binafsi tiketi ya msaada, tutaweza kukusaidia wakati wowote una tatizo na programu yetu. Sisi ni ovyo wako kutatua tatizo lolote kukutana na pH7CMS.',
    'looking_hosting' =>'Kuangalia kwa ajili ya jeshi Web sambamba na pH7CMS? Angalia <a href="' . Controller::SOFTWARE_HOSTING_LIST_URL . '" target="_blank">orodha ya mwenyeji</a>!',
    'error_get_server_url' => 'Shida ya mapatano na anayekutimizia wavuti.<br />
    Tafadhali hakikisha kuwa una wavuti (internet).',
    'powered' => 'Imetolewa na',
    'loading' => 'Subiri...',
);
