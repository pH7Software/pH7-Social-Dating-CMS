<?php
/**
 * @title            Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Inc
 * @version          1.7
 */

defined('PH7') or exit('Restricted access');

/**
 * Get the list of name of directories inside a directory.
 *
 * @param string $sDir
 * @return array
 */
function get_dir_list($sDir)
{
    $aDirList = array();

    if ($rHandle = opendir($sDir))
    {
        while (false !== ($sFile = readdir($rHandle)))
        {
            if ($sFile != '.' && $sFile != '..' && is_dir($sDir . '/' . $sFile))
                $aDirList[] = $sFile;
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
 * @return boolean
 */
function is_directory($sDir)
{
    $sPathProtected = check_ext_start(check_ext_end(trim($sDir)));
    if (is_dir($sPathProtected))
        if (is_writable($sPathProtected))
            return true;
    return false;
}

/**
 * Check start extension.
 *
 * @param string $sDir
 * @return string The good extension.
 */
function check_ext_start($sDir)
{
    if (is_windows()) return $sDir;

    if (substr($sDir, 0, 1) != '/')
        return '/' . $sDir;
    return $sDir;
}

/**
 * Check end extension.
 *
 * @param string $sDir
 * @return string The good extension.
 */
function check_ext_end($sDir)
{
    if (substr($sDir, -1) != PH7_DS)
        return $sDir . PH7_DS;
    return $sDir;
}

/**
 * Validate username.
 *
 * @param string $sUsername
 * @param integer $iMin Default 4
 * @param integer $iMax Default 40
 * @return string (ok, empty, tooshort, toolong, badusername).
 */
function validate_username($sUsername, $iMin = 4, $iMax = 40)
{
    if (empty($sUsername)) return 'empty';
    elseif (strlen($sUsername) < $iMin) return 'tooshort';
    elseif (strlen($sUsername) > $iMax) return 'toolong';
    elseif (preg_match('/[^\w]+$/', $sUsername)) return 'badusername';
    else return 'ok';
}

/**
 * Validate password.
 *
 * @param string $sPassword
 * @param integer $iMin 6
 * @param integer $iMax 92
 * @return string (ok, empty, tooshort, toolong, nonumber, noupper).
 */
function validate_password($sPassword, $iMin = 6, $iMax = 92)
{
    if (empty($sPassword)) return 'empty';
    elseif (strlen($sPassword) < $iMin) return 'tooshort';
    elseif (strlen($sPassword) > $iMax) return 'toolong';
    elseif (!preg_match('/[0-9]{1,}/', $sPassword)) return 'nonumber';
    elseif (!preg_match('/[A-Z]{1,}/', $sPassword)) return 'noupper';
    else return 'ok';
}

/**
 * Validate email.
 *
 * @param string $sEmail
 * @return string (ok, empty, bademail).
 */
function validate_email($sEmail)
{
    if ($sEmail == '') return 'empty';
    if (filter_var($sEmail, FILTER_VALIDATE_EMAIL)== false) return 'bademail';
    else return 'ok';
}

/**
 * Validate name (first name and last name).
 *
 * @param string $sName
 * @param integer $iMin Default 2
 * @param integer $iMax Default 30
 * @return boolean
 */
function validate_name($sName, $iMin = 2, $iMax = 30)
{
    if (is_string($sName) && strlen($sName) >= $iMin && strlen($sName) <= $iMax)
        return true;
    return false;
}

/**
 * Check that all fields are filled.
 *
 * @param array $aVars
 * @return boolean
 */
function filled_out($aVars)
{
    foreach ($aVars as $sKey => $sValue)
        if ((!isset($sKey)) || (trim($sValue) == ''))
            return false;
    return true;
}

/**
 * Check a string identical.
 *
 * @param string $sVal1
 * @param string $sVal2
 * @return boolean
 */
function validate_identical($sVal1, $sVal2)
{
    return ($sVal1 === $sVal2);
}

/**
 * Redirect to another URL.
 *
 * @param string $sUrl
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
 * @return boolean
 */
function delete_dir($sPath)
{
    return is_file($sPath) ?
        @unlink($sPath) :
        is_dir($sPath) ?
        array_map('delete_dir', glob($sPath.'/*')) === @rmdir($sPath) :
        false;
}

/**
 * Executes SQL queries.
 *
 * @param object PDO
 * @param string $sSqlFile SQL File.
 * @return mixed (boolean | array) Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
 */
function exec_query_file($oDb, $sSqlFile)
{
    if (!is_file($sSqlFile)) return false;

    $sSqlContent = file_get_contents($sSqlFile);
    $sSqlContent = str_replace(PH7_TABLE_PREFIX, $_SESSION['db']['db_prefix'], $sSqlContent);
    $rStmt = $oDb->exec($sSqlContent);
    unset($sSqlContent);

    return ($rStmt === false) ? $rStmt->errorInfo() : true;
}

/**
 * Delete install folder.
 *
 * @return void
 */
function remove_install_dir()
{
    // Delete the _install/ directory
    @chmod(PH7_ROOT_INSTALL, 0777);
    delete_dir(PH7_ROOT_INSTALL);
    @rmdir(PH7_ROOT_INSTALL);
}

/**
 * Generate Hash.
 *
 * @param integer $iLength Default 80
 * @return string The random hash. Maximum 128 characters with whirlpool encryption.
 */
function generate_hash($iLength = 80)
{
    return substr(hash('whirlpool', time() . hash('sha512', getenv('REMOTE_ADDR') . uniqid(mt_rand(), true) . microtime(true)*999999999999)), 0, $iLength);
}

/**
 * Check the URL rewrite file (.htaccess).
 *
 * @return boolean
 */
function is_url_rewrite()
{
    return is_file(PH7_ROOT_INSTALL . '.htaccess');
}

/**
 * Check if the OS is Windows.
 *
 * @return boolean
 */
function is_windows()
{
    return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

/**
 * Get the URL contents with CURL.
 *
 * @param string $sFile
 * @return mixed (string | boolean) Return the result content on success, FALSE on failure.
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
 * @return boolean
 */
function zip_extract($sFile, $sDir)
{
    $oZip = new \ZipArchive;

    $mRes = $oZip->open($sFile);

    if ($mRes === true)
    {
        $oZip->extractTo($sDir);
        $oZip->close();
        return true;
    }

    return false; // Return error value
}

/**
 * Check valid URL.
 *
 * @return string $sUrl
 * @return boolean
 */
function check_url($sUrl)
{
    // Checks if URL is valid with HTTP status code '200 OK' or '301 Moved Permanently'
    $aUrl = @get_headers($sUrl);
    return (strpos($aUrl[0], '200 OK') || strpos($aUrl[0], '301 Moved Permanently'));
}

/**
 * Check license key.
 *
 * @param string $sValue The License Key.
 * @return boolean
 */
function check_license($sValue)
{
    $sValue = trim($sValue);
    if (!preg_match('/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}$/', $sValue))
        $bStatus = false;
    elseif (substr($sValue,8,1)*substr($sValue,10,1)*substr($sValue,12,1)*substr($sValue,13,1) != substr($sValue,15,4))
        $bStatus = false;
    else
        $bStatus = true;

    return $bStatus;
}

/**
 * Send an email (text and HTML format).
 *
 * @param array $aParams The parameters information to send email.
 * @return boolean Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
 */
function send_mail($aParams)
{
    // Frontier to separate the text part and the HTML part.
    $sFrontier = "-----=" . md5(mt_rand());

    // Removing any HTML tags to get a text format.
    // If any of our lines are larger than 70 characterse, we return to the new line.
    $sTextBody =  wordwrap(strip_tags($aParams['body']), 70);

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
    if (empty($aParams['from']))
        $aParams['from'] = $_SERVER['SERVER_ADMIN'];

    /*** Headers ***/
    // To avoid the email goes in the spam folder of email client.
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
    return mail($aParams['to'], $aParams['subject'], $sBody, $sHeaders);
}
