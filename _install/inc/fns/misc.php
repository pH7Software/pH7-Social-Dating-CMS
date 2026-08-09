<?php
/**
 * @title            Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Inc
 * @version          1.7
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/**
 * Get the list of name of directories inside a directory.
 *
 * @param string $sDir
 *
 * @return array
 */
function get_dir_list($sDir)
{
    $aDirList = array();

    if ($rHandle = opendir($sDir)) {
        while (false !== ($sFile = readdir($rHandle))) {
            if ($sFile !== '.' && $sFile !== '..' && is_dir($sDir . '/' . $sFile)) {
                $aDirList[] = $sFile;
            }
        }
        closedir($rHandle);
        asort($aDirList);
        reset($aDirList);
    }

    return $aDirList;
}

/**
 * Check valid directory.
 *
 * @param string $sDir
 *
 * @return bool
 */
function is_directory($sDir)
{
    $sPathProtected = check_ext_start(check_ext_end(trim($sDir)));

    if (is_dir($sPathProtected)) {
        if (is_readable($sPathProtected)) {
            return true;
        }
    }
    return false;
}

/**
 * Check start extension.
 *
 * @param string $sDir
 *
 * @return string The good extension.
 */
function check_ext_start($sDir)
{
    return (!is_windows() && substr($sDir, 0, 1) !== '/') ? '/' . $sDir : $sDir;
}

/**
 * Check end extension.
 *
 * @param string $sDir
 *
 * @return string The good extension.
 */
function check_ext_end($sDir)
{
    return substr($sDir, -1) !== PH7_DS ? $sDir . PH7_DS : $sDir;
}

/**
 * Validate name (first and last name).
 *
 * @param string $sName
 * @param int $iMin Default 2
 * @param int $iMax Default 20
 *
 * @return bool
 */
function validate_name($sName, $iMin = 2, $iMax = 20)
{
    return (is_string($sName) && mb_strlen($sName) >= $iMin && mb_strlen($sName) <= $iMax);
}

/**
 * Validate username.
 *
 * @param string $sUsername
 * @param int $iMin Default 3
 * @param int $iMax Default 30
 *
 * @return int (0 = OK | 1 = too short | 2 = too long | 3 = bad username).
 */
function validate_username($sUsername, $iMin = 3, $iMax = 30)
{
    if (mb_strlen($sUsername) < $iMin) return 1;
    elseif (mb_strlen($sUsername) > $iMax) return 2;
    elseif (!preg_match('/^[\w-]+$/', $sUsername)) return 3;
    else return 0;
}

/**
 * Validate password.
 *
 * @param string $sPassword
 * @param int $iMin 12
 * @param int $iMax 92
 *
 * @return int (0 = OK | 1 = too short | 2 = too long | 3 = no number | 4 = no upper).
 */
function validate_password($sPassword, $iMin = 12, $iMax = 92)
{
    if (mb_strlen($sPassword) < $iMin) return 1;
    elseif (mb_strlen($sPassword) > $iMax) return 2;
    elseif (!preg_match('/[0-9]{1,}/', $sPassword)) return 3;
    elseif (!preg_match('/[A-Z]{1,}/', $sPassword)) return 4;
    else return 0;
}

/**
 * Validate email.
 *
 * @param string $sEmail
 * @param int $iEmailMaxLength
 *
 * @return bool
 */
function validate_email($sEmail, $iEmailMaxLength = 120)
{
    return (filter_var($sEmail, FILTER_VALIDATE_EMAIL) !== false && mb_strlen($sEmail) < $iEmailMaxLength);
}

/**
 * Check a string identical.
 *
 * @param string $sVal1
 * @param string $sVal2
 *
 * @return bool
 */
function validate_identical($sVal1, $sVal2)
{
    return ($sVal1 === $sVal2);
}

/**
 * Find a word in a sentence.
 *
 * @param string $sText Sentence.
 * @param string $sWord Word to find.
 *
 * @return bool Returns TRUE if the word is found, FALSE otherwise.
 */
function find($sText, $sWord)
{
    return false !== stripos($sText, $sWord);
}

/**
 * Check that all fields are filled.
 *
 * @param array $aVars
 *
 * @return bool
 */
function filled_out(array $aVars)
{
    foreach ($aVars as $sKey => $sVal) {
        if (empty($sKey) || trim($sVal) === '') {
            return false;
        }
    }
    return true;
}

/**
 * Redirect to another URL.
 *
 * @param string $sUrl
 *
 * @return void
 */
function redirect($sUrl)
{
    header('Location: ' . $sUrl);
    exit;
}

/**
 * Delete directory.
 *
 * @param string $sPath
 *
 * @return bool
 */
function delete_dir(string $sPath): bool
{
    if (is_link($sPath) || is_file($sPath)) {
        return @unlink($sPath);
    }

    if (!is_dir($sPath)) {
        return false;
    }

    $aEntries = scandir($sPath);
    if ($aEntries === false) {
        return false;
    }

    $bDeleted = true;
    foreach ($aEntries as $sEntry) {
        if ($sEntry === '.' || $sEntry === '..') {
            continue;
        }

        if (!delete_dir($sPath . PH7_DS . $sEntry)) {
            $bDeleted = false;
        }
    }

    return $bDeleted && @rmdir($sPath);
}

/**
 * Read the durable, non-secret installer progress record.
 */
function get_install_state(): array
{
    $sStatePath = PH7_ROOT_INSTALL . 'data/caches/install-state.json';
    if (!is_file($sStatePath) || !is_readable($sStatePath)) {
        return ['completed_step' => 0];
    }

    $sState = file_get_contents($sStatePath);
    $aState = is_string($sState) ? json_decode($sState, true) : null;
    if (!is_array($aState) || !isset($aState['completed_step']) || !is_int($aState['completed_step'])) {
        return ['completed_step' => 0];
    }

    $iRecordedStep = max(0, min(6, $aState['completed_step']));
    $iCompletedStep = $iRecordedStep;
    $aContext = isset($aState['context']) && is_array($aState['context']) ? $aState['context'] : [];

    if ($iCompletedStep >= 3 && !is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
        $iCompletedStep = 0;
    } elseif ($iCompletedStep >= 3 && !has_valid_protected_install_path($aContext)) {
        $iCompletedStep = 2;
    } elseif ($iCompletedStep >= 4 && !has_installed_database_config($aContext)) {
        $iCompletedStep = 3;
    }

    $aState['completed_step'] = $iCompletedStep;
    $aState['context'] = sanitize_install_state_context($aContext, $iCompletedStep);
    if ($iCompletedStep !== $iRecordedStep) {
        $aState['recovered_from_step'] = $iRecordedStep;
    }

    return $aState;
}

/**
 * Verify the configuration artifact required after installer step 4.
 */
function has_installed_database_config(array $aContext): bool
{
    foreach (get_protected_install_path_candidates($aContext) as $sProtectedPath) {
        $sConfigPath = $sProtectedPath . 'app/configs/config.ini';
        if (!is_file($sConfigPath) || !is_readable($sConfigPath)) {
            continue;
        }

        $aConfig = parse_ini_file($sConfigPath, true, INI_SCANNER_TYPED);
        if (is_array($aConfig) && isset($aConfig['database']) && is_array($aConfig['database']) &&
            isset(
                $aConfig['database']['type'],
                $aConfig['database']['hostname'],
                $aConfig['database']['username'],
                $aConfig['database']['password'],
                $aConfig['database']['name'],
                $aConfig['database']['prefix'],
                $aConfig['database']['charset'],
                $aConfig['database']['port']
            )
        ) {
            return true;
        }
    }

    return false;
}

function has_valid_protected_install_path(array $aContext): bool
{
    foreach (get_protected_install_path_candidates($aContext) as $sProtectedPath) {
        if (is_file($sProtectedPath . 'app/configs/constants.php')) {
            return true;
        }
    }

    return false;
}

function get_protected_install_path_candidates(array $aContext): array
{
    $aProtectedCandidates = [];
    if (isset($aContext['protected_path']) && is_string($aContext['protected_path'])) {
        $aProtectedCandidates[] = check_ext_end($aContext['protected_path']);
    }
    $aProtectedCandidates[] = PH7_ROOT_PUBLIC . '_protected' . PH7_DS;
    $aProtectedCandidates[] = dirname(PH7_ROOT_PUBLIC) . PH7_DS . '_protected' . PH7_DS;

    return array_unique($aProtectedCandidates);
}

/**
 * Keep only validated, non-secret context needed after the effective step.
 */
function sanitize_install_state_context(array $aContext, int $iCompletedStep): array
{
    $aSafeContext = [];

    if ($iCompletedStep >= 3 && isset($aContext['protected_path']) && is_string($aContext['protected_path'])) {
        $sRealProtectedPath = realpath(rtrim($aContext['protected_path'], '/\\'));
        if (is_string($sRealProtectedPath) && is_dir($sRealProtectedPath)) {
            $aSafeContext['protected_path'] = check_ext_end($sRealProtectedPath);
        }
    }
    if ($iCompletedStep >= 4 && isset($aContext['database_prefix']) && is_string($aContext['database_prefix']) &&
        preg_match('/^[a-z][a-z0-9_]{0,31}$/i', $aContext['database_prefix']) === 1
    ) {
        $aSafeContext['database_prefix'] = $aContext['database_prefix'];
    }
    if ($iCompletedStep >= 5 && isset($aContext['admin_login_email']) && is_string($aContext['admin_login_email']) &&
        filter_var($aContext['admin_login_email'], FILTER_VALIDATE_EMAIL) !== false
    ) {
        $aSafeContext['admin_login_email'] = $aContext['admin_login_email'];
    }
    if ($iCompletedStep >= 5 && isset($aContext['admin_username']) && is_string($aContext['admin_username']) &&
        preg_match('/^[\w-]{3,30}$/', $aContext['admin_username']) === 1
    ) {
        $aSafeContext['admin_username'] = $aContext['admin_username'];
    }

    return $aSafeContext;
}

/**
 * Persist installer progress without database credentials or passwords.
 */
function save_install_state(int $iCompletedStep, array $aContext = []): bool
{
    if ($iCompletedStep < 2 || $iCompletedStep > 6) {
        return false;
    }

    $aPreviousState = get_install_state();
    $aState = [
        'completed_step' => max($iCompletedStep, (int)$aPreviousState['completed_step'])
    ];

    $aPreviousContext = isset($aPreviousState['context']) && is_array($aPreviousState['context'])
        ? $aPreviousState['context']
        : [];
    $aState['context'] = sanitize_install_state_context(
        array_merge($aPreviousContext, $aContext),
        $aState['completed_step']
    );

    $sState = json_encode($aState, JSON_UNESCAPED_SLASHES);
    if (!is_string($sState)) {
        return false;
    }

    $sStatePath = PH7_ROOT_INSTALL . 'data/caches/install-state.json';
    $sTemporaryPath = $sStatePath . '.installing-' . bin2hex(random_bytes(8));
    if (@file_put_contents($sTemporaryPath, $sState . PHP_EOL, LOCK_EX) === false) {
        return false;
    }

    @chmod($sTemporaryPath, 0600);
    if (!@rename($sTemporaryPath, $sStatePath)) {
        @unlink($sTemporaryPath);

        return false;
    }

    return true;
}

/**
 * Executes SQL queries.
 *
 * @param Database $oDb
 * @param string $sSqlFile SQL File.
 *
 * @return bool|array Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
 */
function exec_query_file(Database $oDb, $sSqlFile)
{
    if (!is_file($sSqlFile) || !is_readable($sSqlFile)) {
        return false;
    }

    $sSqlContent = file_get_contents($sSqlFile);
    if (!is_string($sSqlContent) || trim($sSqlContent) === '') {
        return false;
    }
    $sSqlContent = str_replace(PH7_TABLE_PREFIX, $_SESSION['db']['prefix'], $sSqlContent);
    $rStmt = $oDb->exec($sSqlContent);
    unset($sSqlContent);

    return ($rStmt === false) ? $oDb->errorInfo() : true;
}

/**
 * Delete the install folder.
 *
 * @return bool
 */
function remove_install_dir(): bool
{
    $sPublicRoot = realpath(PH7_ROOT_PUBLIC);
    $sInstallRoot = realpath(PH7_ROOT_INSTALL);

    if ($sPublicRoot === false || $sInstallRoot === false) {
        return false;
    }

    $sExpectedInstallRoot = $sPublicRoot . PH7_DS . '_install';
    if (!hash_equals($sExpectedInstallRoot, $sInstallRoot)) {
        error_log('Installer cleanup refused an unexpected path: ' . $sInstallRoot);

        return false;
    }

    $sQuarantinePath = $sPublicRoot . PH7_DS . '.ph7builder-install-removal-' . bin2hex(random_bytes(16));
    if (!@rename($sInstallRoot, $sQuarantinePath)) {
        error_log('Installer cleanup could not quarantine the _install directory. Remove it manually.');

        return false;
    }

    if (!delete_dir($sQuarantinePath)) {
        error_log('Installer cleanup left a quarantined directory that must be removed manually: ' . $sQuarantinePath);
    }

    // The routable _install path is gone even if best-effort quarantine cleanup
    // could not remove every entry.
    return true;
}

/**
 * Get the client IP address.
 *
 * @return string
 */
function client_ip()
{
    $mRemoteAddress = $_SERVER['REMOTE_ADDR'] ?? null;

    return is_string($mRemoteAddress) && filter_var($mRemoteAddress, FILTER_VALIDATE_IP) !== false
        ? $mRemoteAddress
        : '0.0.0.0';
}

/**
 * Escape string.
 *
 * @param string $sVal
 *
 * @return string The escaped string.
 */
function escape($sVal)
{
    return htmlspecialchars($sVal, ENT_QUOTES);
}

/**
 * Clean string.
 *
 * @param string $sVal
 *
 * @return string The cleaned string.
 */
function clean_string($sVal)
{
    return strtr(
        $sVal,
        ["\\" => "\\\\", '$' => '\\$', "\r" => '', "\n" => '', '"' => '\\"']
    );
}

/**
 * Generate Hash.
 *
 * @param int $iLength Default 80
 *
 * @return string The random hash. Maximum 128 characters with whirlpool encryption.
 */
function generate_hash(int $iLength = 80): string
{
    if ($iLength < 1 || $iLength > 128) {
        throw new \InvalidArgumentException('The generated hash length must be between 1 and 128 characters.');
    }

    return substr(bin2hex(random_bytes((int)ceil($iLength / 2))), 0, $iLength);
}

/**
 * Try to find and get the FFmpeg path if it is installed (note I don't use system command like "which ffmpeg" for portability reason).
 *
 * @return string The appropriate FFmpeg path.
 */
function ffmpeg_path()
{
    if (is_windows()) {
        $aPaths = ['C:\ffmpeg\bin\ffmpeg.exe', 'C:\ffmpeg\ffmpeg.exe'];
    } else {
        $aPaths = ['/usr/local/bin/ffmpeg', '/usr/bin/ffmpeg'];
    }

    foreach ($aPaths as $sPath) {
        if (is_file($sPath) && is_executable($sPath)) {
            return $sPath;
        }
    }

    return '';
}

/**
 * Check if Apache's mod_rewrite is installed.
 *
 * @return bool
 */
function is_url_rewrite()
{
    if (!is_file(PH7_ROOT_INSTALL . '.htaccess')) {
        return false;
    }

    // The active .htaccess or web-server configuration sets this marker.
    // Do not probe a request-derived URL: query-string routes are the safe fallback.
    return strtolower((string)getenv('HTTP_MOD_REWRITE')) === 'on';
}

/**
 * Check if the OS is Windows.
 *
 * @return bool
 */
function is_windows()
{
    return 0 === stripos(PHP_OS, 'WIN');
}

/**
 * Checks if the URL is valid and contains the HTTP status code '200 OK', '301 Moved Permanently' or '302 Found'
 *
 * @param string $sUrl
 *
 * @return bool
 */
function check_url($sUrl)
{
    $rCurl = curl_init();
    curl_setopt_array($rCurl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $sUrl]);
    curl_exec($rCurl);
    $iResponse = (int)curl_getinfo($rCurl, CURLINFO_HTTP_CODE);
    curl_close($rCurl);

    return $iResponse === 200 || $iResponse === 301 || $iResponse === 302;
}

/**
 * @param string $sCtrlName
 * @param string $sAction
 *
 * @return bool
 */
function is_software_installed($sCtrlName, $sAction)
{
    return is_file(PH7_ROOT_PUBLIC . '_constants.php') &&
        (int)get_install_state()['completed_step'] === 0 &&
        $sCtrlName === 'InstallController' &&
        in_array($sAction, array('index', 'license'), true);
}

/**
 * Send an email (text and HTML format).
 *
 * @param array $aParams The parameters information to send email.
 *
 * @return bool Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
 */
function send_mail(array $aParams): bool
{
    foreach (['to', 'subject', 'body'] as $sRequiredParameter) {
        if (!isset($aParams[$sRequiredParameter]) || !is_string($aParams[$sRequiredParameter])) {
            return false;
        }
    }

    if (filter_var($aParams['to'], FILTER_VALIDATE_EMAIL) === false ||
        preg_match('/[\r\n]/', $aParams['subject']) === 1
    ) {
        return false;
    }

    $sServerAdmin = is_string($_SERVER['SERVER_ADMIN'] ?? null) ? $_SERVER['SERVER_ADMIN'] : '';
    if (filter_var($sServerAdmin, FILTER_VALIDATE_EMAIL) === false) {
        $sServerAdmin = $aParams['to'];
    }

    $sFrom = isset($aParams['from']) && is_string($aParams['from']) ? $aParams['from'] : $sServerAdmin;
    if (filter_var($sFrom, FILTER_VALIDATE_EMAIL) === false) {
        $sFrom = $sServerAdmin;
    }

    // Frontier to separate the text part and the HTML part.
    $sFrontier = '-----=' . bin2hex(random_bytes(16));

    // Removing any HTML tags to get a text format.
    // If any of our lines are larger than 70 characters, we return to the new line.
    $sTextBody = wordwrap(strip_tags($aParams['body']), 70);

    // HTML format (you can change the layout below).
    $sEscapedSubject = htmlspecialchars($aParams['subject'], ENT_QUOTES, 'UTF-8');
    $sHtmlBody = <<<EOF
<html>
  <head>
    <title>{$sEscapedSubject}</title>
  </head>
  <body>
    <div style="text-align:center">{$aParams['body']}</div>
  </body>
</html>
EOF;

    /*** Headers ***/
    $sHeaders = 'From: "pH7Builder" <' . $sServerAdmin . ">\r\n";
    $sHeaders .= 'Reply-To: <' . $sFrom . ">\r\n";
    $sHeaders .= "MIME-Version: 1.0\r\n";
    $sHeaders .= "Content-Type: multipart/alternative; boundary=\"$sFrontier\"\r\n";

    /*** Text Format ***/
    $sBody = "--$sFrontier\r\n";
    $sBody .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $sBody .= "Content-Transfer-Encoding: 8bit\r\n";
    $sBody .= "\r\n" . $sTextBody . "\r\n";

    /*** HTML Format ***/
    $sBody .= "--$sFrontier\r\n";
    $sBody .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
    $sBody .= "Content-Transfer-Encoding: 8bit\r\n";
    $sBody .= "\r\n" . $sHtmlBody . "\r\n";

    $sBody .= "--$sFrontier--\r\n";

    /** Send Email ***/
    return @mail($aParams['to'], $aParams['subject'], $sBody, $sHeaders);
}
