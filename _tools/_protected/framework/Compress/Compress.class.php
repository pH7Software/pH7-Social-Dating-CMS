<?php
/**
 * @title            Compress Class
 * @desc             This class that compresses the data.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @author           Some pieces of code are inspired by Schepp Christian Schaefer's script (CSS JS booster).
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Compress
 */

namespace PH7\Framework\Compress;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Compress\ValueObject\FileType;
use PH7\Framework\Config\Config;
use PH7\Framework\Url\Url;

class Compress
{
    const COMPRESSION_LEVEL = 6;
    const COMPRESSION_BYTE_BUFFER_SIZE = 2048;
    const GOOGLE_CLOSURE_HOST = 'closure-compiler.appspot.com';
    const GOOGLE_CLOSURE_PARAMS = 'js_code=%s&compilation_level=SIMPLE_OPTIMIZATIONS&output_format=text&output_info=compiled_code';
    const GOOGLE_CLOSURE_PORT = 80;

    const MAX_LIMIT_SIZE_GOOGLE_CLOSURE = 200000; // 200KB

    /**
     * For Stylesheet and JavaScript.
     *
     * The YUI Compressor path where it is stored.
     *
     * @var string $sYuiCompressorPath
     */
    private $sYuiCompressorPath;

    /**
     * For JavaScript Only.
     *
     * The Google Closure Compiler path where it is stored.
     *
     * @var string $sClosureCompilerPath
     */
    private $sClosureCompilerPath;

    /**
     * Temporary File Path.
     *
     * @var string $sTmpFilePath
     */
    private $sTmpFilePath;

    /**
     * Enable or Disabled Google Closure Compiler Service (https://closure-compiler.appspot.com )for the JS files.
     * If you use for too many files at the same time, Google might break it.
     *
     * @var boolean $bIsGoogleClosure
     */
    private $bIsGoogleClosure;

    /**
     * Enable Java Engine Compiler.
     *
     * @var boolean $bJavaCompiler
     */
    private $bJavaCompiler;

    public function __construct()
    {
        $sUniqIdPrefix = (string)mt_rand();

        $this->sYuiCompressorPath = realpath(__DIR__) . '/Compiler/YUICompressor-2.4.7.jar';
        $this->sClosureCompilerPath = realpath(__DIR__) . '/Compiler/ClosureCompiler.jar';
        $this->sTmpFilePath = PH7_PATH_TMP . PH7_DS . uniqid($sUniqIdPrefix, true) . '.tmp';
        $this->bJavaCompiler = (bool)Config::getInstance()->values['cache']['enable.static.minify_java_compiler'];
        $this->bIsGoogleClosure = (bool)Config::getInstance()->values['cache']['enable.js.closure_compiler_service'];
    }

    /**
     * Removing PHP comments.
     *
     * @param string $sPhpCode
     *
     * @return string
     */
    public function parsePhp($sPhpCode)
    {
        $sPhpCode = preg_replace('#/\*.*+\*#', '', $sPhpCode);
        $sPhpCode = $this->parseHtml($sPhpCode);

        return $sPhpCode;
    }

    public function parseHtml($sHtml)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $sHtml, $aPre); // Exclude pre or code tags
        $sHtml = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $sHtml); // Removing all pre or code tags
        $sHtml = preg_replace('#<!--[^\[].+-->#', '', $sHtml); // Removing HTML comments
        $sHtml = preg_replace('/[\n\t\r]+/', '', $sHtml); // Remove new lines, spaces, tabs
        $sHtml = preg_replace('/>[\s]+</', '> <', $sHtml); // Remove new lines, spaces, tabs
        $sHtml = preg_replace('/[\s]+/', ' ', $sHtml); // Remove new lines, spaces, tabs
        $sHtml = preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#', '', $sHtml);
        if (!empty($aPre[0])) {
            foreach ($aPre[0] as $sTag) {
                $sHtml = preg_replace('!#pre#!', $sTag, $sHtml, 1); // Putting back pre|code tags
            }
        }

        return $sHtml;
    }

    public function parseCss($sContent)
    {
        if ($this->bJavaCompiler) {
            file_put_contents($this->sTmpFilePath, $sContent);
            $oFileType = new FileType(FileType::CSS_TYPE);
            $sCssMinified = $this->executeYuiCompressor($oFileType);
            unlink($this->sTmpFilePath);
        } else {
            // Backup any values within single or double quotes
            preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $sContent, $aHit, PREG_PATTERN_ORDER);

            for ($i = 0, $iCountHit = count($aHit[1]); $i < $iCountHit; $i++) {
                $sContent = str_replace($aHit[1][$i], '##########' . $i . '##########', $sContent);
            }
            // Remove traling semicolon of selector's last property
            $sContent = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $sContent);
            // Remove any whitespaces/tabs/newlines between semicolon and property-name
            $sContent = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $sContent);
            // Remove any whitespaces/tabs/newlines surrounding property-colon
            $sContent = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $sContent);
            // Remove any whitespaces/tabs/newlines surrounding selector-comma
            $sContent = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $sContent);
            // Remove any whitespaces/tabs/newlines surrounding opening parenthesis
            $sContent = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $sContent);
            // Remove any whitespaces/tabs/newlines between numbers and units
            $sContent = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $sContent);
            // Shorten zero-values
            $sContent = preg_replace('/([^\d\.]0)(em|%)/ims', '$1', $sContent);
            // Constrain multiple newlines
            $sContent = preg_replace('/[\r\n]+/ims', "\n", $sContent);
            // Constrain multiple whitespaces
            $sContent = preg_replace('/\p{Zs}+/ims', ' ', $sContent);
            // Remove comments
            $sContent = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $sContent);
            // Remove tabs, spaces, newlines, etc. */
            $aArr = ["\r\n", "\r", "\n", "\t", "  ", "    ", "    "];
            $sContent = str_replace($aArr, "", $sContent);
            // Restore backupped values within single or double quotes

            for ($i = 0, $iCountHit = count($aHit[1]); $i < $iCountHit; $i++) {
                $sContent = str_replace('##########' . $i . '##########', $aHit[1][$i], $sContent);
            }

            /**
             * Inclusion of Minify_CSS_Compressor
             */
            $sCssMinified = Minify\Css::process($sContent);
        }

        return $sCssMinified;
    }

    public function parseJs($sContent)
    {
        if ($this->bJavaCompiler) {
            file_put_contents($this->sTmpFilePath, $sContent);
            $oFileType = new FileType(FileType::JS_TYPE);
            $sJsMinified = $this->executeYuiCompressor($oFileType);
            unlink($this->sTmpFilePath);
        } else {
            // URL-encoded file contents
            $sContentEncoded = Url::encode($sContent);

            // If we can open connection to Google Closure
            // Google Closure has a max limit of 200KB POST size, and will break JS with eval-command
            if ($rSocket = $this->googleClosureEligible($sContent, $sContentEncoded)) {
                // Working vars
                $sJsMinified = '';
                $sServiceUri = '/compile';
                $sVars = sprintf(self::GOOGLE_CLOSURE_PARAMS, $sContentEncoded);

                // Compose HTTP request header
                $sHeader = 'Host: ' . self::GOOGLE_CLOSURE_HOST . "\r\n";
                $sHeader .= "User-Agent: PHP Script\r\n";
                $sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $sHeader .= 'Content-Length: ' . strlen($sVars) . "\r\n";
                $sHeader .= "Connection: close\r\n\r\n";

                fwrite($rSocket, "POST $sServiceUri  HTTP/1.0\r\n");
                fwrite($rSocket, $sHeader . $sVars);
                while (!feof($rSocket)) {
                    $sJsMinified .= fgets($rSocket);
                }
                fclose($rSocket);
                $sJsMinified = preg_replace('/^HTTP.+[\r\n]{2}/ims', '', $sJsMinified);
            } else {
                // Remove comments
                //$sContent = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $sContent);

                // Remove tabs, spaces, etc. */
                $sContent = str_replace(["\r", "\t", '  ', '    ', '     '], '', $sContent);

                // Remove other spaces before/after ) */
                $sContent = preg_replace(['(( )+\))', '(\)( )+)'], ')', $sContent);

                /**
                 * Inclusion of Douglas Crockford's JSMin
                 */
                $sJsMinified = Minify\Js::minify($sContent);
            }
        }

        return $sJsMinified;
    }

    /**
     * zlib-compressed output.
     *
     * These "zlib output compression" compress the pages.
     * It save your bandwidth and gives faster download of the pages.
     * WARNING: It can consume high CPU resources on the server.
     * So it might be wise not to use this method if the server isn't so powerful.
     *
     * @return void
     */
    public static function setZlipCompression()
    {
        ini_set('zlib.output_compression', self::COMPRESSION_LEVEL);
        ini_set('zlib.output_compression_level', self::COMPRESSION_BYTE_BUFFER_SIZE);
    }

    /**
     * @param string $sContent
     * @param string $sContentEncoded
     *
     * @return resource|bool Returns the resource if eligible, FALSE otherwise.
     */
    private function googleClosureEligible($sContent, $sContentEncoded)
    {
        if ($this->bIsGoogleClosure &&
            strlen($sContentEncoded) < static::MAX_LIMIT_SIZE_GOOGLE_CLOSURE &&
            preg_match('/[^a-z]eval\(/ism', $sContent) == 0
        ) {
            return @pfsockopen(self::GOOGLE_CLOSURE_HOST, self::GOOGLE_CLOSURE_PORT);
        }

        return false;
    }

    /**
     * @param FileType $oType
     *
     * @return void
     */
    private function executeYuiCompressor(FileType $oType)
    {
        exec(
            sprintf(
                'java -jar %s %s --type %s --charset utf-8',
                $this->sYuiCompressorPath,
                $this->sTmpFilePath,
                $oType->getValue()
            )
        );
    }
}
