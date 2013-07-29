<?php
/**
 * @author      Pierre-Henry Soria
 * @email       pierrehs@hotmail.com
 * @link        http://github.com/pH-7/Nav-Doc-Script-V2
 * @copyright   (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license     CC-BY - http://creativecommons.org/licenses/by/3.0/
 */

namespace PH7\Doc;
defined('PH7') or exit('Restricted access');

/**
 * @desc Detect the user's preferred language.
 * @return string The first two lowercase letter of the browser language.
 */
function get_browser_lang()
{
    $aLang = explode(',' ,@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    return escape(strtolower(substr(chop($aLang[0]), 0, 2)));
}

/**
 * @desc Display a page if the file exists, otherwise displays a 404.
 * @param string $sPage The page.
 * @return void
 */
function get_page($sPage)
{
    if (is_file($sPage))
    {
        $sPage = file_get_contents($sPage);
        echo parse_var($sPage);
    }
    else
    {   // Page Not Found!
        error_404();
    }
}

/**
 * @desc Parse the text to transform variable.
 * @param string $sContent The text.
 * @return string The text parsed.
 */
function parse_var($sContent)
{
    $sContent = str_replace('{site_url}', RELATIVE, $sContent);
    $sContent = str_replace('{static_url}', STATIC_URL, $sContent);
    $sContent = str_replace('{tpl_name}', TPL, $sContent);
    $sContent = str_replace('{site_name}', SITE_NAME, $sContent);
    $sContent = str_replace('{page_name}', get_page_name(), $sContent);
    $sContent = str_replace('{menu_links}', get_links_html(), $sContent);
    $sContent = str_replace('{menu_langs}', get_langs_html(), $sContent);

    return $sContent;
}

/**
 * @desc Get the page name.
 * @return string
 */
function get_page_name()
{
    if (empty($_GET['p']))
    {
        $sName = SITE_SLOGAN;
    }
    else
    {
        $sPageName = str_replace(array('-','_'), ' ', $_GET['p']);
        $sName = ucfirst($sPageName);
    }

    return $sName;
}

/**
 * @see set_lang()
 * @return string The language available.
 */
function get_lang()
{
    return set_lang();
}

/**
 * @param string $sDir The directory.
 * @return string The list of the folder that is in the directory.
 */
function get_dir_list($sDir)
{
    $aDirList = array();

    if ($rHandle = opendir($sDir))
    {
        while(false !== ($sFile = readdir($rHandle)))
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
 * @return string The current URL.
 */
function get_current_url()
{
    return PROT_URL . strip_tags($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
}

/**
 * @desc Check if the language folder and the language core folder exists.
 * @return string The language available.
 */
function set_lang()
{
    if (!empty($_GET['l']) && is_file(DATA_PATH . $_GET['l'] . '/core/welcome.tpl') && is_file(DATA_PATH . $_GET['l'] . '/core/404-error.tpl'))
    {
        setcookie('pH7_doc_lang', $_GET['l'], time()+60*60*24*365, null, null, false, true);
        $sLang = $_GET['l'];
    }
    elseif (isset($_COOKIE['pH7_doc_lang']) && is_dir(DATA_PATH . $_COOKIE['pH7_doc_lang'] . '/core/'))
    {
        $sLang = $_COOKIE['pH7_doc_lang'];
    }
    elseif (is_dir(DATA_PATH . get_browser_lang() . '/core/'))
    {
        $sLang = get_browser_lang();
    }
    else
    {
        $sLang = DEF_LANG;
    }

    return $sLang;
}

/**
 * @desc Escape function with htmlspecialchars() PHP function.
 * @return string
 */
function escape($sVal)
{
    return htmlspecialchars($sVal, ENT_QUOTES);
}

/**
 * @desc Sets an error 404 page with HTTP 404 code status.
 * @return void
 */
function error_404()
{
    header('HTTP/1.1 404 Not Found');
    get_page(DATA_PATH . LANG . '/core/404-error.tpl');
}
