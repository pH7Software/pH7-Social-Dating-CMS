<?php
/**
 * @title            Hungarian Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / HU
 * @update           09/29/13 16:53
 */

namespace PH7;

$LANG = array (
    'lang' => 'hu',
    'charset' => 'utf-8',
    'lang_name' => 'Magyar',
    'version' => 'verzió',
    'CMS_desc' => '<p>Üdvözöllek a ' . Controller::SOFTWARE_NAME . ' telepítőjében.<br />
    Köszönjük, hogy a CMS-ünket választottad, és remélem, hogy elnyeri a tetszésed.</p>
    <p>Kérlek kövesd a hét lépésből álló telepítés útmutatónkat.</p>',
    'choose_install_lang' => 'Kérlek válaszd ki a nyelved a telepítés elkezdéséhez',
    'requirements_desc' => 'FIGYELMEZTETÉS! Kérlek győződj meg arról, hogy a szervered eleget tesz a <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">követelményeknek</a> ami elengedhetetlen a CMS helyes működéséhez.',
    'config_path' => '&quot;protected&quot; könyvtár elérése',
    'desc_config_path' => 'Kérlek add meg a teljes elérési útját a &quot;protected&quot; könyvtárnak.<br />
    Ajánlott ezt a könyvtárt a nyilvános gyökérkönyvtáron kívülre helyezni a Webszervereden.',
    'need_frame' => 'A böngésződnek támogatnia kell az inline frames technológiát!',
    'path_protected' => 'Path of the &quot;protected&quot; folder',
    'next' => 'Következő',
    'go' => 'Következő lépés =>',
    'license' => 'Licenc',
    'license_desc' => 'Please read the license carefully and accept it before continuing the installation of the software!',
    'registration_for_license' => 'Kérlek regisztrálj <a href="' . Controller::SOFTWARE_REGISTRATION_URL . '" target="_blank">ezen az oldalon</a> az ingyenes licenchez, és egyúttal a folytatáshoz.',
    'your_license' => 'A licenc kulcsod',
    'agree_license' => 'Elolvastam és elfogadom a Feltételeket.',
    'step' => 'Lépés',
    'welcome' => 'Üdvözöllek a következő telepítőjében:',
    'welcome_to_installer' => 'A következő telepítője:',
    'config_site' => 'Állítsd be a webhelyedet!',
    'config_system' => 'A CMS rendszer beállítása!',
    'bad_email' => 'Helytelen e-mail cím!',
    'finish' => 'Telepítés befejezése!',
    'go_your_site' => 'Tovább az új webhelyedhez!',
    'go_your_admin_panel' => 'Menj az adminisztrációs weboldalára!',
    'error_page_not_found' => 'Oldal nem található',
    'error_page_not_found_desc' => 'Sajnáljuk, de az oldal amit keresel nem található.',
    'success_license' => 'A licenc kulcsod helyes!',
    'failure_license' => 'Sajnáljuk, de a licenc kulcsod helytelen!',
    'no_protected_exist' => 'Sajnáljuk, de nem tudtuk megtalálni a &quot;protected&quot; könyvtárt.',
    'no_protected_readable' => 'Kérlek változtasd meg a &quot;protected&quot; mappa jogosultságait a következőre: (CHMOD 755).',
    'no_public_writable' => 'Kérlek változtasd meg a &quot;public&quot; mappa jogosultságait a következőre: (CHMOD 777).',
    'no_app_config_writable' => 'Kérlek változtasd meg a &quot;protected/app/configs&quot; mappa jogosultságait a következőre:  (CHMOD 777).',
    'database_error' => 'Sikertelen adatbázishoz kapcsolódás.<br />',
    'error_sql_import' => 'Hiba történt a fájl adatbázisba importálása közben.',
    'require_mysql_version' => 'A MYSQL verziónak ' . PH7_REQUIRE_SQL_VERSION . ' vagy magasabbnak kell lennie a telepítésheez.',
    'field_required' => 'Ez a mező kötelező',
    'all_fields_mandatory' => 'Minden mező ami csillaggal (*) van jelölve kötelező',
    'db_hostname' => 'Adatbázis szerver hosztneve',
    'desc_db_hostname' => '(Általában &quot;localhost&quot; vagy &quot;127.0.0.1&quot;)',
    'db_name' =>'Az adatbázis neve',
    'db_username' => 'Felhasználónév az adatbázishoz',
    'db_password' => 'Adatbázis Jelszó',
    'db_prefix' => 'Tábla előtagok az adatbázisban',
    'desc_db_prefix' => 'Ez a funkció akkor lehet hasznos, ha több pH7CMS is egy azon adatbázisba van telepítve. Azért mi javasolnánk, hogy változtasd meg az alapértéket a biztonság növelésének érdekében.',
    'db_encoding' => 'Kódolás',
    'desc_db_encoding' => 'Adatbázis kódolás, Általában UTF8 van alkalmazva a nemzetköziséghez.',
    'db_port' => 'Az adatbázis portja',
    'ffmpeg_path' => 'Elérési út az FFmpeg indítófájljához (ha nem tudod ez hol van, kérdezd meg a szolgáltatód)',
    'passwords_different' => 'A jelszó megerősítés nem egyezik az eredetivel',
    'username_bad_username' => 'A felhasználóneved helytelen',
    'username_too_short' => 'A felhasználóneved túl rövid, minimum 4 karakter hosszúnak kell lennie',
    'username_too_long' => 'A felhasználóneved túl hosszú, maximum 40 karakter hosszú lehet',
    'password_no_number' => 'A jelszavadnak minimum egy számot kell tartalmaznia',
    'password_no_upper' => 'A jelszavadban minimum egy nagybetűnek kell lennie',
    'password_too_short' => 'A jelszavad túl rövid',
    'password_too_long' => 'A jelszavad túl hosszú',
    'bug_report_email' => 'Hiba jelentése e-mailben',
    'admin_first_name' => 'Keresztneved',
    'admin_last_name' => 'Vezetékneved',
    'admin_username' => 'A felhasználóneved az adminisztrátori pult bejelentkezéséhez',
    'admin_login_email' => 'A jelszavad az adminisztrátori pult bejelentkezéséhez',
    'admin_email' => 'Az adminisztrációhoz felhasznált e-mail címed',
    'admin_return_email' => 'Nevalaszolj e-mail cím (általában nevalaszolj@teoldalad.hu)',
    'admin_feedback_email' => 'E-mail cím a kapcsolatfelvételi űrlaphoz (visszajelzés)',
    'admin_password' => 'A jelszavad',
    'admin_passwords' => 'Kérlek erősítsd meg a jelszavad',
    'bad_first_name' => 'Kérlek add meg a keresztneved, ami 2 és 20 karakter között kell, hogy legyen.',
    'bad_last_name'=> 'Kérlek add meg a vezetékneved, ami 2 és 20 karakter között kell, hogy legyen.',
    'remove_install_folder_auto' => 'Automatikus törlése az &quot;install&quot; könyvtárnak (ehhez a művelethez hozzáférési jogok kellenek az &quot;install&quot; könyvtár törléséhez).',
    'confirm_remove_install_folder_auto' => 'FIGYELMEZTETÉS, Minden fájl a /_install/ könyvtárban törlésre kerül.',
    'remove_install_folder' => 'Kérjük, távolítsa el a &quot;_install&quot; mappát a kiszolgálón, mielőtt használni a weboldalán.',
    'title_email_finish_install' => 'Gratulálunk, a webhelyed telepítése elkészült!',
    'content_email_finish_install' => '<p><strong>Gratulálunk, a webhelyed telepítése elkészült!</strong></p>
    <p>Reméljük élvezni fogod a <em>' . Controller::SOFTWARE_NAME . '</em> használatát!</p>
    <p>Hibajelentéshez, javaslatokhoz, ötletekhez, partnerséghez, a fejlesztésben való részvételhez, fordításban segédkezéshez
    kérlek látogass el a <a href="' . Controller::SOFTWARE_WEBSITE . '" target="_blank">webhelyünkre</a>.</p>
    <p>---</p>
    <p>Köszönettel,</p>
    <p>A pH7CMS fejlesztőcsapata.</p>',
    'yes_dir' => 'A könyvtár sikeresen megtalálva!',
    'no_dir' => 'A könyvtár nem létezik.',
    'wait_importing_database' => 'Kérlek várj amíg az adatbázis importálás befejeződik.<br />
    Ez eltarthat néhány percig is.',
    'service' => 'Hasznos kiegészítő szolgáltatások',
    'buy_copyright_license_title' => 'Vegyél egy engedély',
    'buy_copyright_license' => '<span class="bold italic">320£</span> <span class="gray">Egy alkalommal</span> <span class="right">Vásárlás</span>',
    'buy_copyright_license_desc' => 'A vásárló egy licenckulcs, akkor nem lesz olyan linkeket, és szerzői jogi figyelmeztetést weboldalán.',
    'buy_individual_ticket_support_title' => 'Vegyél egy egyéni támogató szolgálat',
    'buy_individual_ticket_support' => '<span class="bold italic">55£</span> <span class="gray">Teljes jegy támogatás egy hónapra</span> <span class="right">Vásárlás</span>',
    'buy_individual_ticket_support_desc' => 'Megvásárlásával egy egyéni jegyet támogatás, segítünk, ha van egy probléma a szoftver. Mi az Ön rendelkezésére megoldani minden problémát találkozás pH7CMS.',
    'looking_hosting' =>'Keresek egy Web Host kompatibilis pH7CMS? Tekintse <a href="' . Controller::SOFTWARE_HOSTING_LIST_URL . '" target="_blank">meg a listát hosting</a>!',
    'error_get_server_url' => 'Hozzáférési problémák a Webszerverünkhöz.<br />
    Kérjük ellenőrizd, hogy a webszervered hozzáfér az internethez, egyéb esetben kérlek várj (lehetséges, hogy a mi szerverünk túl van terhelve).',
    'powered' => 'A CMS készítője a',
    'loading' => 'Töltés...',
);
