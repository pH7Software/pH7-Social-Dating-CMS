<?php
 /**
 * @title            Compress Class
 * @desc             This class that compresses the data.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @author           Some pieces of code are inspired by Schepp Christian Schaefer's script (CSS JS booster).
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Compress
 * @version          0.9
 */

namespace PH7\Framework\Compress;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;

class Compress
{

    /**
     * For Stylesheet and JavaScript.
     *
     * The YUI Compressor path where it is stored.
     *
     * @var string $_sYuiCompressorPath
     */
    private $_sYuiCompressorPath;

    /**
     * For JavaScript Only.
     *
     * The Google Closure Compiler path where it is stored.
     *
     * @var string $_sClosureCompilerPath
     */
    private $_sClosureCompilerPath;

    /**
     * Temporary File Path.
     *
     * @var string $_sTmpFilePath
     */
    private $_sTmpFilePath;

    /**
     * Enable or Disabled Google Closure Compiler Service (https://closure-compiler.appspot.com )for the JS files.
     * If you use for too many files at the same time, Google might break it.
     *
     * @var boolean $_bIsGoogleClosure
     */
    private $_bIsGoogleClosure;

    /**
     * Enable Java Engine Compiler.
     *
     * @var boolean $_bJavaCompiler
     */
    private $_bJavaCompiler;

    public function __construct()
    {
        $this->_sYuiCompressorPath = realpath(__DIR__) . '/Compiler/YUICompressor-2.4.7.jar';
        $this->_sClosureCompilerPath = realpath(__DIR__) . '/Compiler/ClosureCompiler.jar';
        $this->_sTmpFilePath = PH7_PATH_TMP . PH7_DS . uniqid() . '.tmp';
        $this->_bJavaCompiler = (bool) Config::getInstance()->values['cache']['enable.static.minify_java_compiler'];
        $this->_bIsGoogleClosure = (bool) Config::getInstance()->values['cache']['enable.js.closure_compiler_service'];
    }

    public function parsePhp($sPhp)
    {
        $sPhp = preg_replace('#/\*.*+\*#', '', $sPhp); # Removing PHP comments
        $sPhp = $this->parseHtml($sPhp);
        return $sPhp;
    }

    public function parseHtml($sHtml)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $sHtml, $aPre); # Exclude pre or code tags
        $sHtml = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $sHtml); # Removing all pre or code tags
        $sHtml = preg_replace('#<!--[^\[].+-->#', '', $sHtml); # Removing HTML comments
        $sHtml = preg_replace('/[\n\t\r]+/', '', $sHtml); # Remove new lines, spaces, tabs
        $sHtml = preg_replace('/>[\s]+</', '> <', $sHtml); # Remove new lines, spaces, tabs
        $sHtml = preg_replace('/[\s]+/', ' ', $sHtml); # Remove new lines, spaces, tabs
        $sHtml = preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#', '', $sHtml);
        if (!empty($aPre[0]))
            foreach ($aPre[0] as $sTag)
                $sHtml = preg_replace('!#pre#!', $sTag, $sHtml, 1);# Putting back pre|code tags
        return $sHtml;
    }

    public function parseCss($sContent)
    {
        if ($this->_bJavaCompiler)
        {
            file_put_contents($this->_sTmpFilePath, $sContent);
            $sCssMinified = exec("java -jar $this->_sYuiCompressorPath $this->_sTmpFilePath --type css --charset utf-8");
            unlink($this->_sTmpFilePath);
        }
        else
        {
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
            // remove comments
            $sContent = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $sContent);
            // remove tabs, spaces, newlines, etc. */
            $aArr = array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ");
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
        if ($this->_bJavaCompiler)
        {
            file_put_contents($this->_sTmpFilePath, $sContent);
            $sJsMinified = exec("java -jar $this->_sYuiCompressorPath $this->_sTmpFilePath --type js --charset utf-8");
            unlink($this->_sTmpFilePath);
        }
        else
        {
            // If we can open connection to Google Closure
            // Google Closure has a max limit of 200KB POST size, and will break JS with eval-command

            // URL-encoded file contents
            $sContentEncoded = \PH7\Framework\Url\Url::encode($sContent);
            // Closure Host
            $sHost = 'closure-compiler.appspot.com';

            if ($this->_bIsGoogleClosure && strlen($sContentEncoded) < 200000 && preg_match('/[^a-z]eval\(/ism', $sContent) == 0 && $rSocket = @pfsockopen($sHost, 80))
            {
                // Working vars
                $sJsMinified = '';
                $sServiceUri = '/compile';
                $sVars = 'js_code=' . $sContentEncoded . '&compilation_level=SIMPLE_OPTIMIZATIONS&output_format=text&output_info=compiled_code';

                // Compose HTTP request header
                $sHeader = "Host: $sHost\r\n";
                $sHeader .= "User-Agent: PHP Script\r\n";
                $sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $sHeader .= "Content-Length: " . strlen($sVars) . "\r\n";
                $sHeader .= "Connection: close\r\n\r\n";

                fputs($rSocket, "POST $sServiceUri  HTTP/1.0\r\n");
                fputs($rSocket, $sHeader . $sVars);
                while (!feof($rSocket)) $sJsMinified .= fgets($rSocket);
                fclose($rSocket);
                $sJsMinified = preg_replace('/^HTTP.+[\r\n]{2}/ims', '', $sJsMinified);
            }
            // Switching over to Douglas Crockford's JSMin (which in turn breaks IE's conditional compilation)
            else
            {
                // remove comments
                //$sContent = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $sContent);

                // remove tabs, spaces, etc. */
                $sContent = str_replace(array("\r", "\t", '  ', '    ', '     '), '', $sContent);

                // remove other spaces before/after ) */
                $sContent = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $sContent);

                /**
                 * Inclusion of JSMin
                 */
                $sJsMinified = Minify\Js::minify($sContent);
            }
        }

        return $sJsMinified;
    }

}
