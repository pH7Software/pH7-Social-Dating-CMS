<?php
/**
 * @title            Lorem Ipsum Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Str / Generator
 * @version          1.1
 */

namespace PH7\Framework\Str\Generator;
defined('PH7') or exit('Restricted access');

class LoremIpsum
{

    const
    PLAIN_FORMAT = 1,
    TEXT_FORMAT = 2,
    HTML_FORMAT = 3;

    private
    $_iTotalWords = 0,
    $_sContents = '';

    /**
     * Constructor.
     *
     * @param integer $iTotalWords Total characters.
     * @param integer $iFormat Constant: PLAIN_FORMAT, TEXT_FORMAT, HTML_FORMAT
     */
    public function __construct($iTotalWords, $iFormat)
    {
        $this->_iTotalWords = (int) $iTotalWords;

        switch($iFormat)
        {
            case static::PLAIN_FORMAT:
                $this->_sContents = $this->getPlain();
            break;

            case static::TEXT_FORMAT:
                $this->_sContents = $this->getText();
            break;

            case static::HTML_FORMAT:
                $this->_sContents = $this->getHtml();
            break;

            default:
                throw new \PH7\Framework\Str\Exception('Output Format for "Lorem Ipsum" is invalid!');
        }
    }

    /**
     * Output.
     *
     * @return string The contents.
     */
    public function __toString()
    {
        return $this->_sContents;
    }

    protected function getPlain()
    {
        $sText = $this->_getPlain();
        return str_replace(array("\n", "\t", "r"), '', $sText);
    }

    protected function getText()
    {
        return $this->_getPlain();
    }

    protected function getHtml()
    {
        $sText = $this->_getPlain();
        $sText = str_replace("\n", "</p>\n<p>", $sText);
        return '<p>' . $sText . '</p>';
    }

    private function _getWords()
    {
        $aWords = array();
        $sDictPath = __DIR__ . '/loremipsum.txt';
        $aDoctWords = file($sDictPath);

        for ($i = 0; $i < $this->_iTotalWords; $i++)
        {
            $iIndex = array_rand($aDoctWords);
            $sWord = str_replace(array("\n", "\r"), '', $aDoctWords[$iIndex]);

            if ($i > 0 && $aWords[$i-1] == $sWord)
                $i--;
            else
                $aWords[$i] = $sWord;
        }

        return $aWords;
    }

    private function _getPlain()
    {
        $sOutputText = '';
        $aWords = $this->_getWords();

        for ($i = 0, $iN = count($aWords); $i < $iN; $i++)
        {
            $sDelimiter =  ($i%12==4 ? ', ' : ($i%12==8 ? '. ' : ($i%12==1 ? "\n" : ' ')));
            $sOutputText .= ($i%12==9 ? ucfirst($aWords[$i]) : $aWords[$i]) . $sDelimiter;
        }

        return $sOutputText;
    }

}
