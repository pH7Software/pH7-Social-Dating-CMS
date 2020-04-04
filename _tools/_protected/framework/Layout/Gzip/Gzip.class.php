<?php
/**
 * @title            Gzip Class
 * @desc             Compression and optimization of static files.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Gzip
 * @version          1.7
 */

namespace PH7\Framework\Layout\Gzip;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Compress\Compress;
use PH7\Framework\Config\Config;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;
use PH7\Framework\File\Permission\PermissionException;
use PH7\Framework\Http\Http;
use PH7\Framework\Layout\Optimization;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Navigation\Browser;
use Teapot\StatusCode;

class Gzip
{
    const REGEX_IMAGE_FORMAT = '/url\([\'"]*(.+?\.)(gif|png|jpg|jpeg|otf|eot|ttf|woff|svg)[\'"]*\)*/msi';
    const REGEX_JS_INCLUDE_FORMAT = '/include\([\'"]*(.+?\.)(js)[\'"]*\)\s{0,};/msi';
    const REGEX_CSS_IMPORT_FORMAT = '/@import\s+url\([\'"]*(.+?\.)(css)[\'"]*\)\s{0,};/msi';
    const CACHE_DIR = 'pH7_static/';
    const MAX_IMG_SIZE_BASE64_CONVERTOR = 24000; // 24KB
    const GZIP_COMPRESS_LEVEL = 9;

    const HTML_NAME = 'html';
    const CSS_NAME = 'css';
    const JS_ABBR_NAME = 'js';
    const JS_NAME = 'javascript';

    const ASSET_FILES_ACCEPTED = [
        self::HTML_NAME,
        self::CSS_NAME,
        self::JS_ABBR_NAME
    ];


    /** @var File */
    private $oFile;

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var string */
    private $sBase;

    /** @var string */
    private $sBaseUrl;

    /** @var string */
    private $sType;

    /** @var string */
    private $sDir;

    /** @var string */
    private $sFiles;

    /** @var string */
    private $sContents;

    /** @var string */
    private $sCacheDir;

    /** @var array */
    private $aElements;

    /** @var integer */
    private $iIfModified;

    /** @var boolean */
    private $bCaching;

    /** @var boolean */
    private $bCompressor;

    /** @var boolean */
    private $bDataUri;

    /** @var boolean */
    private $bGzipContent;

    /** @var boolean */
    private $bIsGzip;

    /** @var string|boolean */
    private $mEncoding;

    public function __construct()
    {
        $this->oFile = new File;
        $this->oHttpRequest = new HttpRequest;

        $this->bCaching = (bool)Config::getInstance()->values['cache']['enable.static.cache'];
        $this->bCompressor = (bool)Config::getInstance()->values['cache']['enable.static.minify'];
        $this->bGzipContent = (bool)Config::getInstance()->values['cache']['enable.static.gzip_compress'];
        $this->bDataUri = (bool)Config::getInstance()->values['cache']['enable.static.data_uri'];

        $this->bIsGzip = $this->isGzip();
    }

    /**
     * Set cache directory.
     * If the directory is not correct, the method will cause an exception.
     * If you do not use this method, a default directory will be created.
     *
     * @param string $sCacheDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException If the cache directory does not exist.
     */
    public function setCacheDir($sCacheDir)
    {
        if (is_dir($sCacheDir)) {
            $this->sCacheDir = $sCacheDir;
        } else {
            throw new PH7InvalidArgumentException(
                sprintf('%s cache directory cannot be found!', $sCacheDir)
            );
        }
    }

    /**
     * Displays compressed files.
     *
     * @return void
     *
     * @throws Exception If the cache file couldn't be written or read.
     */
    public function run()
    {
        if (!$this->isValidStaticTypeFile()) {
            Http::setHeadersByCode(StatusCode::SERVICE_UNAVAILABLE);
            exit('Invalid file type!');
        }

        $this->sType = ($this->oHttpRequest->get('t') === self::JS_ABBR_NAME) ? self::JS_NAME : $this->oHttpRequest->get('t');

        // Directory
        if (!$this->oHttpRequest->getExists('d')) {
            Http::setHeadersByCode(StatusCode::SERVICE_UNAVAILABLE);
            exit('No directory specified!');
        }

        $this->sDir = $this->oHttpRequest->get('d');
        $this->sBase = $this->oFile->checkExtDir(realpath($this->sDir));
        $this->sBaseUrl = $this->clearUrl($this->oFile->checkExtDir($this->sDir));

        // The Files
        if (!$this->oHttpRequest->getExists('f')) {
            Http::setHeadersByCode(StatusCode::SERVICE_UNAVAILABLE);
            exit('No file specified!');
        }

        $this->sFiles = $this->oHttpRequest->get('f');
        $this->aElements = explode(',', $this->sFiles);

        foreach ($this->aElements as $sElement) {
            $sPath = realpath($this->sBase . $sElement);

            if (!$this->isValidStaticFileExtension($sPath)) {
                Http::setHeadersByCode(StatusCode::FORBIDDEN);
                exit('Invalid file extension!');
            }

            if (!$this->isSourceStaticFileExists($sPath)) {
                Http::setHeadersByCode(StatusCode::NOT_FOUND);
                exit('File not found!');
            }
        }

        $this->setHeaders();

        // If the cache is enabled, reads cache and displays, otherwise reads and displays the contents.
        $this->bCaching ? $this->cache() : $this->getContents();

        echo $this->sContents;
    }

    /**
     * Set Caching.
     *
     * @return string The cached contents.
     *
     * @throws PermissionException If the cache directory couldn't be created.
     * @throws Exception If the cache file couldn't be written or read.
     */
    public function cache()
    {
        $this->checkCacheDir();

        $oBrowser = new Browser;

        $this->iIfModified = !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, 29) : null;

        $this->sCacheDir .= $this->oHttpRequest->get('t') . PH7_DS;
        $this->oFile->createDir($this->sCacheDir);
        $sExt = $this->bIsGzip ? 'gz' : 'cache';
        $sCacheFile = md5($this->sType . $this->sDir . $this->sFiles) . PH7_DOT . $sExt;
        $sFullCacheFile = $this->sCacheDir . $sCacheFile;

        foreach ($this->aElements as $sElement) {
            $sSourcePath = realpath($this->sBase . $sElement);

            /**
             * We will try the cache first to see if the combined files were already generated
             */
            if ($this->hasCacheExpired($sSourcePath, $sFullCacheFile)) {
                if ($this->hasHttpHeaderExpired($sSourcePath)) {
                    $oBrowser->noCache();
                }

                // Get contents of the files
                $this->getContents();

                // Store the file in the cache
                if (!$this->oFile->putFile($sFullCacheFile, $this->sContents)) {
                    throw new Exception('Cannot write cache file: ' . $sFullCacheFile);
                }
            }
        }

        if ($this->oHttpRequest->getMethod() !== HttpRequest::METHOD_HEAD) {
            $oBrowser->cache();

            // Warning: following can cause problems (ERR_FILE_NOT_FOUND error)
            // Http::setHeadersByCode(StatusCode::NOT_MODIFIED); // Not Modified header
        }

        unset($oBrowser);

        if (!$this->sContents = $this->oFile->getFile($sFullCacheFile)) {
            throw new Exception('Cannot read cache file: ' . $sFullCacheFile);
        }
    }

    /**
     * Routing for files compressing.
     *
     * @return void
     */
    protected function makeCompress()
    {
        $oCompress = new Compress;

        switch ($this->sType) {
            case self::HTML_NAME:
                $this->sContents = $oCompress->parseHtml($this->sContents);
                break;

            case self::CSS_NAME:
                $this->sContents = $oCompress->parseCss($this->sContents);
                break;

            case self::JS_NAME:
                $this->sContents = $oCompress->parseJs($this->sContents);
                break;

            default:
                Http::setHeadersByCode(StatusCode::SERVICE_UNAVAILABLE);
                exit('Invalid file type!');
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
        $this->sContents = gzencode(
            $this->sContents,
            self::GZIP_COMPRESS_LEVEL,
            FORCE_GZIP
        );
    }

    /**
     * Get contents of the files.
     *
     * @return void
     */
    protected function getContents()
    {
        $this->sContents = '';
        foreach ($this->aElements as $sElement) {
            $this->sContents .= File::EOL . $this->oFile->getUrlContents(PH7_URL_ROOT . $this->sBaseUrl . $sElement);
        }

        if ($this->sType === self::CSS_NAME) {
            $this->parseVariable();
            $this->getSubCssFile();
            $this->getImageIntoCss();
        }

        if ($this->sType === self::JS_NAME) {
            $this->parseVariable();
            $this->getSubJsFile();
        }

        if ($this->bCompressor) {
            $this->makeCompress();
        }

        if ($this->bCaching) {
            $this->setCachedDateHeaderComment();
        }

        if ($this->bIsGzip) {
            $this->gzipContent();
        }
    }

    /**
     * @return void
     */
    protected function setHeaders()
    {
        // Send Content-Type
        header('Content-Type: text/' . $this->sType);
        header('Vary: Accept-Encoding');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24 * 10) . ' GMT'); // 10 days

        // Send compressed contents
        if ($this->bIsGzip) {
            header('Content-Encoding: ' . $this->mEncoding);
        }
    }

    /**
     * Check if gzip is activate.
     *
     * @return boolean Returns FALSE if compression is disabled or is not valid, otherwise returns TRUE
     */
    protected function isGzip()
    {
        $this->mEncoding = (new Browser)->encoding();

        return (!$this->bGzipContent ? false : ($this->mEncoding !== false));
    }

    /**
     * Parser the CSS/JS variables in cascading style sheets and JavaScript files.
     *
     * @return void
     */
    protected function parseVariable()
    {
        $sBaseUrl = $this->sBaseUrl;

        /**
         * $getCurrentTplName is used in "variables.inc.php" file
         */
        $getCurrentTplName = static function () use ($sBaseUrl) {
            $aDirs = explode('/', $sBaseUrl);
            return !empty($aDirs[2]) ? $aDirs[2] : PH7_DEFAULT_THEME;
        };

        $this->setVariables(include('variables.inc.php'));
    }

    /**
     * @return void
     */
    protected function getSubCssFile()
    {
        // We also collect the files included in the CSS files. So we can also cache and compressed.
        preg_match_all(self::REGEX_CSS_IMPORT_FORMAT, $this->sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++) {
            $this->sContents = str_replace($aHit[0][$i], '', $this->sContents);
            $this->sContents .= File::EOL . $this->oFile->getUrlContents($aHit[1][$i] . $aHit[2][$i]);
        }
    }

    /**
     * @return void
     */
    protected function getSubJsFile()
    {
        // We also collect the files included in the JavaScript files. So we can also cache and compressed.
        preg_match_all(self::REGEX_JS_INCLUDE_FORMAT, $this->sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++) {
            $this->sContents = str_replace($aHit[0][$i], '', $this->sContents);
            $this->sContents .= File::EOL . $this->oFile->getUrlContents($aHit[1][$i] . $aHit[2][$i]);
        }
    }

    /**
     * Get the images into the CSS files.
     *
     * @return void
     */
    private function getImageIntoCss()
    {
        preg_match_all(self::REGEX_IMAGE_FORMAT, $this->sContents, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++) {
            $sImgPath = PH7_PATH_ROOT . $this->sBaseUrl . $aHit[1][$i] . $aHit[2][$i];
            $sImgUrl = PH7_URL_ROOT . $this->sBaseUrl . $aHit[1][$i] . $aHit[2][$i];

            if ($this->isDataUriEligible($sImgPath)) {
                $this->sContents = str_replace(
                    $aHit[0][$i],
                    'url(' . Optimization::dataUri($sImgPath, $this->oFile) . ')',
                    $this->sContents
                );
            } else {
                $this->sContents = str_replace(
                    $aHit[0][$i],
                    'url(' . $sImgUrl . ')',
                    $this->sContents
                );
            }
        }
    }

    /**
     * Set CSS/JS variables.
     *
     * @param array $aVars Variable names containing the values.
     *
     * @return void
     */
    private function setVariables(array $aVars)
    {
        // Replace the variable name by the content
        foreach ($aVars as $sKey => $sVal) {
            $this->sContents = str_replace('[$' . $sKey . ']', $sVal, $this->sContents);
        }
    }

    private function setCachedDateHeaderComment()
    {
        $this->sContents = '/*Cached on ' . gmdate('d M Y H:i:s') . '*/' . File::EOL . $this->sContents;
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the directory cache does not exist, it creates a directory.
     *
     * @return void
     */
    private function checkCacheDir()
    {
        $this->sCacheDir = empty($this->sCacheDir) ? PH7_PATH_CACHE . static::CACHE_DIR : $this->sCacheDir;
    }

    /**
     * @param string $sSourcePath The (uncached) source static file.
     *
     * @return bool
     */
    private function isSourceStaticFileExists($sSourcePath)
    {
        return is_file($sSourcePath) && substr($sSourcePath, 0, strlen($this->sBase)) === $this->sBase;
    }

    /**
     * @param string $sSourcePath The (uncached) source static file.
     * @param string $sFullCacheFile
     *
     * @return bool Returns TRUE if the cache has expired, FALSE otherwise.
     */
    private function hasCacheExpired($sSourcePath, $sFullCacheFile)
    {
        return $this->oFile->getModifTime($sSourcePath) > $this->oFile->getModifTime($sFullCacheFile);
    }

    /**
     * @param string $sSourcePath The (uncached) source static file.
     *
     * @return bool
     */
    private function hasHttpHeaderExpired($sSourcePath)
    {
        return !empty($this->iIfModified) && $this->oFile->getModifTime($sSourcePath) > $this->iIfModified;
    }

    /**
     * Returns TRUE if the image-file exists and if file-size is lower than 24 KB
     *
     * @param string $sImgPath
     *
     * @return bool
     */
    private function isDataUriEligible($sImgPath)
    {
        return $this->bDataUri && is_file($sImgPath) &&
            $this->oFile->size($sImgPath) < self::MAX_IMG_SIZE_BASE64_CONVERTOR;
    }

    /**
     * @param string $sPath
     *
     * @return bool
     */
    private function isValidStaticFileExtension($sPath)
    {
        return
            ($this->sType === self::HTML_NAME && substr($sPath, -5) === '.html') ||
            ($this->sType === self::JS_NAME && substr($sPath, -3) === '.js') ||
            ($this->sType === self::CSS_NAME && substr($sPath, -4) === '.css');
    }

    /**
     * Checks if the static type file is valid.
     *
     * @return bool
     */
    private function isValidStaticTypeFile()
    {
        return $this->oHttpRequest->getExists('t') &&
            in_array($this->oHttpRequest->get('t'), self::ASSET_FILES_ACCEPTED, true);
    }

    /**
     * Remove backslashes on Windows.
     *
     * @param string $sPath
     *
     * @return string The path without backslashes and/or double slashes.
     */
    private function clearUrl($sPath)
    {
        return str_replace(['\\', '//'], '/', $sPath);
    }
}
