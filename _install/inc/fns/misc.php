<?php
/**
 * @title            Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
 * @param int $iMin 6
 * @param int $iMax 92
 *
 * @return int (0 = OK | 1 = too short | 2 = too long | 3 = no number | 4 = no upper).
 */
function validate_password($sPassword, $iMin = 6, $iMax = 92)
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
function delete_dir($sPath)
{
    return (
    is_file($sPath) ?
        @unlink($sPath) :
        (is_dir($sPath) ?
            array_map(__NAMESPACE__ . '\delete_dir', glob($sPath . '/*')) === @rmdir($sPath) :
            false)
    );
}

/**
 * Executes SQL queries.
 *
 * @param Db $oDb
 * @param string $sSqlFile SQL File.
 *
 * @return bool|array Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
 */
function exec_query_file(Db $oDb, $sSqlFile)
{
    if (!is_file($sSqlFile)) {
        return false;
    }

    $sSqlContent = file_get_contents($sSqlFile);
    $sSqlContent = str_replace(PH7_TABLE_PREFIX, $_SESSION['db']['prefix'], $sSqlContent);
    $rStmt = $oDb->exec($sSqlContent);
    unset($sSqlContent);

    return ($rStmt === false) ? $oDb->errorInfo() : true;
}

/**
 * Delete the install folder.
 *
 * @return void
 */
function remove_install_dir()
{
    @chmod(PH7_ROOT_INSTALL, 0777);
    delete_dir(PH7_ROOT_INSTALL);
}

/**
 * Get the client IP address.
 *
 * @return string
 */
function client_ip()
{
    $sIp = $_SERVER['REMOTE_ADDR']; // Default value

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $sIp = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $sIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return preg_match('/^[a-z0-9:.]{7,}$/', $sIp) ? $sIp : '0.0.0.0';
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
    return str_replace('"', '\"', $sVal);
}

/**
 * Generate Hash.
 *
 * @param int $iLength Default 80
 *
 * @return string The random hash. Maximum 128 characters with whirlpool encryption.
 */
function generate_hash($iLength = 80)
{
    $sPrefix = (string)mt_rand();

    return substr(
        hash(
            'whirlpool',
            time() . hash('sha512',
                getenv('REMOTE_ADDR') . uniqid($sPrefix, true) . microtime(true) * 999999999999)
        ),
        0,
        $iLength
    );
}

/**
 * Try to find and get the FFmpeg path if it is installed (note I don't use system command like "which ffmpeg" for portability reason).
 *
 * @return string The appropriate FFmpeg path.
 */
function ffmpeg_path()
{
    if (is_windows()) {
        $sPath = is_file('C:\ffmpeg\bin\ffmpeg.exe') ? 'C:\ffmpeg\bin\ffmpeg.exe' : 'C:\ffmpeg\ffmpeg.exe';
    } else {
        $sPath = is_file('/usr/local/bin/ffmpeg') ? '/usr/local/bin/ffmpeg' : '/usr/bin/ffmpeg';
    }

    return $sPath;
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

    // Check if mod_rewrite is installed and is configured to be used via .htaccess
    if (!$bIsRewrite = (strtolower(getenv('HTTP_MOD_REWRITE')) === 'on')) {
        $sOutputMsg = 'mod_rewrite Works!';

        if (!empty($_GET['a']) && $_GET['a'] === 'test_mod_rewrite') {
            exit($sOutputMsg);
        }

        $sPage = @file_get_contents(PH7_URL_INSTALL . 'test_mod_rewrite');
        $bIsRewrite = ($sPage === $sOutputMsg);
    }

    return $bIsRewrite;
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
 * Get the URL contents with CURL.
 *
 * @param string $sFile
 *
 * @return string|bool Returns the result content on success, FALSE on failure.
 */
function get_url_contents($sFile)
{
    $rCh = curl_init();
    curl_setopt($rCh, CURLOPT_URL, $sFile);
    curl_setopt($rCh, CURLOPT_HEADER, 0);
    curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
    $mResult = curl_exec($rCh);
    curl_close($rCh);
    unset($rCh);

    return $mResult;
}

/**
 * Extract Zip archive.
 *
 * @param string $sFile Zip file.
 * @param string $sDir Destination to extract the file.
 *
 * @return bool
 */
function zip_extract($sFile, $sDir)
{
    $oZip = new \ZipArchive;

    $mRes = $oZip->open($sFile);

    if ($mRes === true) {
        $oZip->extractTo($sDir);
        $oZip->close();
        return true;
    }

    return false; // Return error value
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
        $sCtrlName === 'InstallController' &&
        in_array($sAction, array('index', 'license'), true);
}

/**
 * Check license key.
 *
 * @param string $sKey The License Key.
 *
 * @return int
 */
function check_license($sKey)
{
    $sKey = strtolower(trim($sKey));

    return preg_match('/^ph7-[a-z0-9]{36}$/', $sKey);
}

/**
 * @param string $sTweetMsg
 * @param string $sTwitterUsername
 * @param string $sGitRepoUrl
 *
 * @return string
 */
function get_tweet_post($sTweetMsg, $sTwitterUsername, $sGitRepoUrl)
{
    $sTwitterTweetUrl = 'https://twitter.com/intent/tweet?text=';
    $sMsg = sprintf($sTweetMsg, $sTwitterUsername, $sGitRepoUrl);

    return $sTwitterTweetUrl . urlencode($sMsg);
}

/**
 * Send an email (text and HTML format).
 *
 * @param array $aParams The parameters information to send email.
 *
 * @return bool Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
 */
function send_mail(array $aParams)
{
    // Frontier to separate the text part and the HTML part.
    $sFrontier = "-----=" . md5(mt_rand());

    // Removing any HTML tags to get a text format.
    // If any of our lines are larger than 70 characters, we return to the new line.
    $sTextBody = wordwrap(strip_tags($aParams['body']), 70);

    // HTML format (you can change the layout below).
    $sHtmlBody = <<<EOF
<html>
  <head>
    <title>{$aParams['subject']}</title>
  </head>
  <body>
    <div style="text-align:center">{$aParams['body']}</div>
  </body>
</html>
EOF;

    // If the email sender is empty, we define the server email.
    if (empty($aParams['from'])) {
        $aParams['from'] = $_SERVER['SERVER_ADMIN'];
    }

    /*** Headers ***/
    // To avoid the email goes to spam folder of email client.
    $sHeaders = "From: \"{$_SERVER['HTTP_HOST']}\" <{$_SERVER['SERVER_ADMIN']}>\r\n";

    $sHeaders .= "Reply-To: <{$aParams['from']}>\r\n";
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
