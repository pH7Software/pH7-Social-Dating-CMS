<?php
/**
 * @title            Russian Language File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Lang / RU
 * @update           09/10/13 23:06
 */

namespace PH7;

$LANG = array (
    'lang' => 'ru',
    'charset' => 'utf-8',
    'lang_name' => 'pусский',
    'version' => 'версия',
    'CMS_desc' => '<p>Добро пожаловать в установку ' . Controller::SOFTWARE_NAME . '.<br />
    Благодарим Вас за выбор нашей CMS, и мы надеемся, что он будет радовать вас.</p>
    <p>Пожалуйста, следуйте семь шаги установки.</p>',
    'choose_install_lang' => 'Пожалуйста, выберите ваш язык, чтобы начать установку',
    'requirements_desc' => 'ВНИМАНИЕ! Пожалуйста, убедитесь, что ваш сервер имеет необходимую <a href="' . Controller::SOFTWARE_REQUIREMENTS_URL . '" target="_blank">требование</a> правильно запустить pH7CMS.',
    'config_path' => '&laquo;protected&raquo; путь к каталогу',
    'desc_config_path' => 'Пожалуйста, укажите полный путь к &laquo;protected&raquo; папка.<br />
    Это мудрое и целесообразно поставить этот каталог за пределами публичного квадратный из вашего веб-сервера.',
    'need_frame' => 'Вы должны использовать веб-браузер, который поддерживает встроенные фреймы!',
    'path_protected' => 'Путь &laquo;protected&raquo; папки',
    'next' => 'Следующий',
    'go' => 'Следующий шаг =>',
    'license' => 'Лицензия',
    'license_desc' => 'Пожалуйста, прочитайте внимательно лицензию и принять его, прежде чем продолжить установку программного обеспечения!',
    'registration_for_license' => 'Пожалуйста, зарегистрируйтесь по этой <a href="' . Controller::SOFTWARE_REGISTRATION_URL . '" target="_blank">странице</a> чтобы получить бесплатную лицензию для продолжения загрузки требуется.',
    'your_license' => 'Лицензионный ключ',
    'agree_license' => 'Я прочитал и согласен с вышеуказанными условиями.',
    'step' => 'Шаг',
    'welcome' => 'Добро пожаловать на установку',
    'welcome_to_installer' => 'Установка',
    'config_site' => 'Настройка вашего сайта!',
    'config_system' => 'Настройка системы CMS!',
    'bad_email' => 'Неверный адрес электронной почты',
    'finish' => 'Завершения установки!',
    'go_your_site' => 'Перейти на новый сайт!',
    'go_your_admin_panel' => 'Перейдите на панель администратора!',
    'error_page_not_found' => 'Страница не найдена',
    'error_page_not_found_desc' => 'Извините, но страница, которую вы ищете, не может быть найден.',
    'success_license' => 'Лицензионный ключ правильно!',
    'failure_license' => 'К сожалению, Ваш лицензионный ключ был неправильным!',
    'no_protected_exist' => 'Извините, но мы не нашли &laquo;protected&raquo; каталог.',
    'no_protected_readable' => 'Пожалуйста, измените права доступа к &laquo;protected&raquo; каталога в режиме чтения (CHMOD 755).',
    'no_public_writable' => 'Пожалуйста, измените права доступа к &laquo;public&raquo; каталога в режиме записи (CHMOD 777).',
    'no_app_config_writable' => 'Пожалуйста, измените права доступа к &laquo;protected/app/configs&raquo; каталога в режиме записи (CHMOD 777).',
    'database_error' => 'Ошибка подключения к базе данных.<br />',
    'error_sql_import' => 'Произошла ошибка при импорте файла в базу данных SQL',
    'require_mysql_version' => 'Вы должны установить MySQL ' . PH7_REQUIRE_SQL_VERSION . ' или выше для того, чтобы продолжить.',
    'field_required' => 'Это поле обязательно',
    'all_fields_mandatory' => 'Все поля, отмеченные звездочкой (*) обязательны для заполнения',
    'db_hostname' => 'Сервер базы данных хоста',
    'desc_db_hostname' => '(В целом &quot;localhost&quot; или &quot;127.0.0.1&quot;)',
    'db_name' =>'Имя базы данных,',
    'db_username' => 'Имя пользователя базы данных',
    'db_password' => 'база паролей',
    'db_prefix' => 'Префикс таблиц в базе данных',
    'desc_db_prefix' => 'Эта опция полезна, когда у вас есть несколько установок pH7CMS на той же базе данных. Мы рекомендуем, чтобы вы по-прежнему изменить значения по умолчанию для того, чтобы повысить безопасность вашего сайта.',
    'db_encoding' => 'Кодирование',
    'desc_db_encoding' => 'База данных кодирования, как правило, кодировка UTF8 для международных.',
    'db_port' => 'Порт базы данных',
    'ffmpeg_path' => 'Путь к исполняемому FFmpeg (если вы не знаете, где он находится, пожалуйста, обратитесь к хост)',
    'passwords_different' => 'Подтверждение пароля не совпадают первоначальный пароль',
    'username_bad_username' => 'Ваше имя пользователя является неправильным',
    'username_too_short' => 'Ваше Имя пользователя слишком короткое, не менее 4 символов',
    'username_too_long' => 'Ваше имя пользователя является слишком долго, максимум 40 символов',
    'password_no_number' => 'Ваш пароль должен содержать хотя бы одну цифру',
    'password_no_upper' => 'Ваш пароль должен содержать как минимум одну заглавную',
    'password_too_short' => 'Ваш пароль является слишком коротким',
    'password_too_long' => 'Ваш пароль слишком длинный',
    'bug_report_email' => 'Электронная почта Сообщение об ошибке',
    'admin_first_name' => 'Ваше имя',
    'admin_last_name' => 'Ваша фамилия',
    'admin_username' => 'Ваше имя пользователя чтоб войти в вашу Панель администратора',
    'admin_login_email' => 'Ваш адрес электронной почты для входа в вашу Панель администратора',
    'admin_email' => 'Ваш адрес электронной почты для администрации',
    'admin_return_email' => 'Noreply адрес электронной почты (как правило noreply@yoursite.com)',
    'admin_feedback_email' => 'Адрес электронной почты для контакта форме (обратная связь)',
    'admin_password' => 'Ваш пароль',
    'admin_passwords' => 'Пожалуйста, подтвердите свой пароль',
    'bad_first_name' => 'Пожалуйста, введите ваше имя, она также должна быть от 2 до 20 символов.',
    'bad_last_name'=> 'Пожалуйста, введите свои фамилию, она также должна быть от 2 до 20 символов.',
    'remove_install_folder_auto' => 'Автоматическое удаление &laquo;install&raquo; каталога (это требует прав доступа, чтобы удалить &laquo;install&raquo; каталог).',
    'confirm_remove_install_folder_auto' => 'ВНИМАНИЕ, ВСЕ файлы в каталоге /_install/ папки будут удалены.',
    'remove_install_folder' => 'Пожалуйста, удалите папку &laquo;_install&raquo; с вашего сервера, прежде чем использовать свой ​​сайт.',
    'title_email_finish_install' => 'Поздравляем, установка вашего сайта закончен!',
    'content_email_finish_install' => '<p><strong>Поздравляем, Ваш сайт в настоящее время успешно установлен!</strong></p>
    <p>Мы надеемся, Вам понравится работать с <em>' . Controller::SOFTWARE_NAME . '</em>!</p>
    <p>Для сообщения об ошибке, предложения, предложения, партнерство, участие в разработке CMS и ее перевод и т.д.,
    пожалуйста, посетите наш <a href="' . Controller::SOFTWARE_WEBSITE . '" target="_blank">веб-сайт</a>.</p>
    <p>---</p>
    <p>С уважением,</p>
    <p>Команда pH7CMS разработчиков.</p>',
    'yes_dir' => 'Каталог был успешно найдены!',
    'no_dir' => 'Каталог не существует.',
    'wait_importing_database' => 'Пожалуйста, подождите при импорте базы данных.<br />
    Это может занять несколько минут.',
    'service' => 'Полезные дополнительные услуги',
    'buy_copyright_license_title' => 'Покупать лицензию',
    'buy_copyright_license' => '<span class="bold italic">£320</span> <span class="gray">Пожизненная лицензия</span> <span class="right">Купить сейчас</span>',
    'buy_copyright_license_desc' => 'По покупая лицензионный ключ, вы не будете иметь никаких связей и уведомления об авторских правах на вашем сайте.',
    'buy_individual_ticket_support_title' => 'Купить индивидуальную службу поддержки',
    'buy_individual_ticket_support' => '<span class="bold italic">£55</span> <span class="gray">Полная поддержка билет на один месяц</span> <span class="right">Купить сейчас</span>',
    'buy_individual_ticket_support_desc' => 'Приобретая индивидуальную поддержку по бронированию билетов, мы вам поможем, когда у вас есть проблемы с нашим программным обеспечением. Мы в вашем распоряжении, чтобы разрешить любую проблему столкновение с pH7CMS.',
    'looking_hosting' =>'Ищете веб-хостинга Совместимость с pH7CMS? Смотрите <a href="' . Controller::SOFTWARE_HOSTING_LIST_URL . '" target="_blank">наш список хостинг</a>!',
    'error_get_server_url' => 'Доступ проблемы с нашими веб-сервера.<br />
    Пожалуйста, убедитесь, что ваш сервер подключен к интернету, в противном случае следует лишь немного подождать (не исключено, что наш сервер перегружен).',
    'powered' => 'Создано',
    'loading' => 'Загрузка...',
);
