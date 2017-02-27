<?php
/**
 * @title            Gzip Class
 * @desc             Compression and optimization of static files.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Gzip
 * @version          1.6
 */

namespace PH7\Framework\Layout\Gzip;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\File,
PH7\Framework\Config\Config,
PH7\Framework\Layout\Optimization,
PH7\Framework\Navigation\Browser,
PH7\Framework\Http\Http,
PH7\Framework\Mvc\Request\Http as HttpRequest;

class Gzip
{
    const CACHE_DIR = 'pH7_static/';

    private
    $_oFile,
    $_oHttpRequest,
    $_sBase,
    $_sBaseUrl,
    $_sType,
    $_sDir,
    $_sFiles,
    $_aElements,
    $_sContents,
    $_iIfModified,
    $_sCacheDir,
    $_bCaching,
    $_bCompressor,
    $_bDataUri,
    $_bGzipContent,
    $_bIsGzip,
    $_mEncoding;

    public function __construct()
    {
        $this->_oFile = new File;
        $this->_oHttpRequest = new HttpRequest;

        $this->_bCaching = (bool) Config::getInstance()->values['cache']['enable.static.cache'];
        $this->_bCompressor = (bool) Config::getInstance()->values['cache']['enable.static.minify'];
        $this->_bGzipContent = (bool) Config::getInstance()->values['cache']['enable.static.gzip_compress'];
        $this->_bDataUri = (bool) Config::getInstance()->values['cache']['enable.static.data_uri'];

        $this->_bIsGzip = $this->isGzip();
    }

    /**
     * Set cache directory.
     * If the directory is not correct, the method will cause an exception.
     * If you do not use this method, a default directory will be created.
     *
     * @param string $sCacheDir
     * @return void
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the cache directory does not exist.
     */
    public function setCacheDir($sCacheDir)
    {
        if (is_dir($sCacheDir))
            $this->_sCacheDir = $sCacheDir;
        else
            throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('"' . $sCacheDir . '" cache directory cannot be found!');
    }

    /**
     * Displays compressed files.
     *
     * @return void
     */
    public function run()
    {
        // Determine the directory and type we should use
        if (!$this->_oHttpRequest->getExists('t') || ($this->_oHttpRequest->get('t') !==
            'html' && $this->_oHttpRequest->get('t') !== 'css' && $this->_oHttpRequest->get('t') !== 'js'))
        {
            Http::setHeadersByCode(503);
            exit('Invalid type file!');
        }
        $this->_sType = ($this->_oHttpRequest->get('t') === 'js') ? 'javascript' : $this->_oHttpRequest->get('t');

        // Directory
        if (!$this->_oHttpRequest->getExists('d'))
        {
            Http::setHeadersByCode(503);
            exit('No directory specified!');
        }

        $this->_sDir = $this->_oHttpRequest->get('d');
        $this->_sBase = $this->_oFile->checkExtDir(realpath($this->_sDir));
        $this->_sBaseUrl = $this->_clearUrl($this->_oFile->checkExtDir($this->_sDir));

        // The Files
        if (!$this->_oHttpRequest->getExists('f'))
        {
            Http::setHeadersByCode(503);
            exit('No file specified!');
        }

        $this->_sFiles = $this->_oHttpRequest->get('f');
        $this->_aElements = explode(',', $this->_sFiles);

        foreach ($this->_aElements as $sElement)
        {
            $sPath = realpath($this->_sBase . $sElement);

            if (($this->_sType == 'html' && substr($sPath, -5) != '.html') || ($this->
                _sType == 'javascript' && substr($sPath, -3) != '.js') || ($this->_sType == 'css' && substr($sPath, -4) != '.css'))
            {
                Http::setHeadersByCode(403);
                exit('Error file extension.');
            }

            if (substr($sPath, 0, strlen($this->_sBase)) != $this->_sBase || !is_file($sPath))
            {
                Http::setHeadersByCode(404);
                exit('The file not found!');
            }
        }

        $this->setHeaders();

        // If the cache is enabled, reads cache and displays, otherwise reads and displays the contents.
        ($this->_bCaching) ? $this->cache() : $this->getContents();

        echo $this->_sContents;
    }

    /**
     * Set Caching.
     *
     * @return string The contents.
     * @throws \PH7\Framework\Layout\Gzip\Exception If the cache file couldn't be written.
     */
    public function cache()
    {
        $this->_checkCacheDir();

        /**
         * Try the cache first to see if the combined files were already generated.
         */

        $oBrowser = new Browser;

        $this->_iIfModified = (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) ? substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, 29) : null;

        $this->_sCacheDir .= $this->_oHttpRequest->get('t') . PH7_DS;
        $this->_oFile->createDir($this->_sCacheDir);
        $sExt = ($this->_bIsGzip) ? 'gz' : 'cache';
        $sCacheFile = md5($this->_sType . $this->_sDir . $this->_sFiles) . PH7_DOT . $sExt;

        foreach ($this->_aElements as $sElement)
        {
            $sPath = realpath($this->_sBase . $sElement);

            if ($this->_oFile->getModifTime($sPath) > $this->_oFile->getModifTime($this->_sCacheDir . $sCacheFile))
            {
                if (!empty($this->_iIfModified) && $this->_oFile->getModifTime($sPath) > $this->_oFile->getModifTime($this->_iIfModified))
                    $oBrowser->noCache();

                // Get contents of the files
                $this->getContents();

                // Store the file in the cache
                if (!$this->_oFile->putFile($this->_sCacheDir . $sCacheFile, $this->_sContents))
                    throw new Exception('Couldn\'t write cache file: \'' . $this->_sCacheDir . $sCacheFile . '\'');
            }
        }

        if ($this->_oHttpRequest->getMethod() != 'HEAD')
        {
            $oBrowser->cache();
            //header('Not Modified', true, 304); // Warning: It can causes problems (ERR_FILE_NOT_FOUND)
        }

        unset($oBrowser);

        if (!$this->_sContents = $this->_oFile->getFile($this->_sCacheDir . $sCacheFile))
            throw new Exception('Couldn\'t read cache file: \'' . $this->_sCacheDir . $sCacheFile . '\'');
    }

    /**
     * Routing for files compressing.
     *
     * @return void
     */
    protected function makeCompress()
    {
        $oCompress = new \PH7\Framework\Compress\Compress;

        switch ($this->_sType)
        {
            case 'html':
                $this->_sContents = $oCompress->parseHtml($this->_sContents);
            break;

            case 'css':
                $this->_sContents = $oCompress->parseCss($this->_sContents);
            break;

            case 'javascript':
                $this->_sContents = $oCompress->parseJs($this->_sContents);
            break;

            default:
                Http::setHeadersByCode(503);
                exit('Invalid type file!');
        }

        unset($oCompress);
    }

    /**
     * Transform the contents into a gzip compressed string.
     *
     * @return void
     */
    protected function gzipContent()
    {
        $this->_sContents = gzencode($this->_sContents, 9, FORCE_GZIP);
    }

    /**
     * Get contents of the files.
     *
     * @return void
     */
    protected function getContents()
    {
        $this->_sContents = '';
        foreach ($this->_aElements as $sElement) {
            $this->_sContents .= File::EOL . $this->_oFile->getUrlContents(PH7_URL_ROOT . $this->_sBaseUrl . $sElement);
        }

        if ($this->_sType == 'css')
        {
            $this->parseVariable();
            $this->getSubCssFile();
            $this->getImgInCssFile();
        }

        if ($this->_sType == 'javascript')
        {
            $this->parseVariable();
            $this->getSubJsFile();
        }

        if ($this->_bCompressor) $this->makeCompress();

        if ($this->_bCaching) $this->_sContents = '/*Cached on ' . gmdate('d M Y H:i:s') . '*/' . File::EOL . $this->_sContents;

        if ($this->_bIsGzip) $this->gzipContent();
    }

    /**
     * Set Headers.
     *
     * @return void
     */
     protected function setHeaders()
     {
        // Send Content-Type
        header('Content-Type: text/' . $this->_sType);
        header('Vary: Accept-Encoding');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600*24*10) . ' GMT');

        // Send compressed contents
        if ($this->_bIsGzip) header('Content-Encoding: ' . $this->_mEncoding);
    }

    /**
     * Check if gzip is activate.
     *
     * @return boolean Returns FALSE if compression is disabled or is not valid, otherwise returns TRUE
     */
    protected function isGzip()
    {
        $this->_mEncoding = (new Browser)->encoding();
        return (!$this->_bGzipContent ? false : ($this->_mEncoding !== false ? true : false));
    }

    /**
     * Parser the CSS/JS variables in cascading style sheets and JavaScript files.
     *
     * @return void
     */
    protected function parseVariable()
    {
        $sBaseUrl = $this->_sBaseUrl;
        $getCurrentTplName = function () use ($sBaseUrl) {
            $aDirs = explode('/', $sBaseUrl);
            return !empty($aDirs[2]) ? $aDirs[2] : PH7_DEFAULT_THEME;
        };

        $this->_setVariables( include('variables.inc.php') );
    }



    /**
     * Get the sub CSS files.
     *
     * @return void
     */
    protected function getSubCssFile()
    {
        // We also collect the files included in the CSS files. So we can also cache and compressed.
        preg_match_all('/@import\s+url\([\'"]*(.+?\.)(css)[\'"]*\)\s{0,};/msi', $this->_sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++)
        {
            $this->_sContents = str_replace($aHit[0][$i], '', $this->_sContents);
            $this->_sContents .= File::EOL . $this->_oFile->getUrlContents($aHit[1][$i] . $aHit[2][$i]);
        }
    }

    /**
     * Get the sub JavaScript files.
     *
     * @return void
     */
    protected function getSubJsFile()
    {
        // We also collect the files included in the JavaScript files. So we can also cache and compressed.
        preg_match_all('/include\([\'"]*(.+?\.)(js)[\'"]*\)\s{0,};/msi', $this->_sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++)
        {
            $this->_sContents = str_replace($aHit[0][$i], '', $this->_sContents);
            $this->_sContents .= File::EOL . $this->_oFile->getUrlContents($aHit[1][$i] . $aHit[2][$i]);
        }
    }

    /**
     * Get the images in the CSS files.
     *
     * @return void
     */
    protected function getImgInCssFile()
    {
        preg_match_all('/url\([\'"]*(.+?\.)(gif|png|jpg|jpeg|otf|eot|ttf|woff|svg)[\'"]*\)*/msi', $this->_sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++)
        {
            $sImgPath = PH7_PATH_ROOT . $this->_sBaseUrl . $aHit[1][$i] . $aHit[2][$i];
            $sImgUrl = PH7_URL_ROOT . $this->_sBaseUrl . $aHit[1][$i] . $aHit[2][$i];

            // If the image-file exists and if file-size is lower than 24 KB, we convert it into base64 data URI
            $this->_sContents = ($this->_bDataUri && is_file($sImgPath) && $this->_oFile->size($sImgPath) <
                24000) ? str_replace($aHit[0][$i], 'url(' . Optimization::dataUri($sImgPath) . ')', $this->_sContents) : str_replace($aHit[0][$i], 'url(' . $sImgUrl . ')', $this->_sContents);
        }
    }

    /**
     * Set CSS/JS variables.
     *
     * @param array $aVars Variable names containing the values.
     * @return void
     */
    private function _setVariables(array $aVals)
    {
        // Replace the variable name by the content
        foreach ($aVals as $sKey => $sVal)
            $this->_sContents = str_replace('[$' . $sKey . ']', $sVal, $this->_sContents);
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the directory cache does not exist, it creates a directory.
     *
     * @return void
     */
    private function _checkCacheDir()
    {
        $this->_sCacheDir = (empty($this->_sCacheDir)) ? PH7_PATH_CACHE . static::CACHE_DIR : $this->_sCacheDir;
    }

    /**
     * Remove backslashes on Windows.
     *
     * @param string $sPath
     * @return string The path without backslashes and/or double slashes.
     */
    private function _clearUrl($sPath)
    {
        return str_replace(array('\\', '//'), '/', $sPath);
    }
}
