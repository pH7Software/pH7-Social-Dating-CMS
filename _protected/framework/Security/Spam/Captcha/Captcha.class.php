<?php
/**
 * @title          Captcha Class
 * @desc           Generates a captcha and manages the display of the image.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / Spam / Captcha
 * @version        0.9
 */

namespace PH7\Framework\Security\Spam\Captcha;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Navigation\Browser;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;

class Captcha
{
    const NUM_CHARACTER_CAPTCHA = 5;
    const NAX_CHARACTER_CAPTCHA = 14;

    const COMPLEXITY_LOW = 5;
    const COMPLEXITY_MEDIUM = 7;
    const COMPLEXITY_HIGH = 9;

    const RELOAD_ICON_FILENAME = 'reload.svg';
    const SESSION_NAME = 'rand_code';

    /** @var Session */
    private $oSession;

    /** @var string */
    private $sStr;

    /** @var string */
    private $sFont;

    /** @var int */
    private $iStringWidth;

    /** @var int */
    private $iHeight;

    /** @var int */
    private $iWidth;

    /** @var int */
    private $iSize = 36;

    /** @var int */
    private $iMargin = 25;

    /** @var array */
    private $aBox;

    /** @var array */
    private static $aMatrixBlur = [
        [1, 1, 1],
        [1, 1, 1],
        [1, 1, 1]
    ];

    /** @var array */
    private $aColor = [];

    /** @var resource */
    private $rImg;

    /** @var resource */
    private $rBlack;

    /** @var resource */
    private $rRed;

    /** @var resource */
    private $rWhite;

    public function __construct()
    {
        $this->oSession = new Session;
    }

    /**
     * Show the captcha image.
     *
     * @param int|null $iRandom
     * @param int $iComplexity
     *
     * @return void
     */
    public function show($iRandom = null, $iComplexity = self::NUM_CHARACTER_CAPTCHA)
    {
        $iComplexity = $this->getCorrectStringLength($iComplexity);

        if (!empty($iRandom)) {
            $this->sStr = Various::genRnd($iRandom, $iComplexity);
        } else {
            $this->sStr = Various::genRnd('pH7_Pierre-Henry_Soria_Sanz_González_captcha', $iComplexity);
        }

        $this->oSession->set(self::SESSION_NAME, $this->sStr);

        $this->sFont = $this->getFont();
        //$sBackground = PH7_PATH_DATA . 'background/' . mt_rand(1, 5) . '.png';

        $this->aBox = imagettfbbox($this->iSize, 0, $this->sFont, $this->sStr);
        $this->iWidth = $this->aBox[2] - $this->aBox[0];
        $this->iHeight = $this->aBox[1] - $this->aBox[7];
        unset($this->aBox);

        $this->iStringWidth = round($this->iWidth / strlen($this->sStr));

        //$this->rImg = imagecreatefrompng($sBackground);
        $this->rImg = imagecreate($this->iWidth + $this->iMargin, $this->iHeight + $this->iMargin);
        $this->aColor = [
            imagecolorallocate($this->rImg, 0x99, 0x00, 0x66),
            imagecolorallocate($this->rImg, 0xCC, 0x00, 0x00),
            imagecolorallocate($this->rImg, 0x00, 0x00, 0xCC),
            imagecolorallocate($this->rImg, 0x00, 0x00, 0xCC),
            imagecolorallocate($this->rImg, 0xBB, 0x88, 0x77)
        ];

        $this->rBlack = imagecolorallocate($this->rImg, 0, 0, 0);
        $this->rRed = imagecolorallocate($this->rImg, 200, 100, 90);
        $this->rWhite = imagecolorallocate($this->rImg, 255, 255, 255);

        imagefilledrectangle($this->rImg, 0, 0, 399, 99, $this->rWhite);

        $this->mixing();

        imageline(
            $this->rImg,
            mt_rand(2, $this->iWidth + $this->iMargin),
            mt_rand(1, $this->iWidth + $this->iMargin),
            mt_rand(1, $this->iHeight + $this->iMargin),
            mt_rand(2, $this->iWidth + $this->iMargin), $this->rBlack
        );

        imageline(
            $this->rImg,
            mt_rand(2, $this->iHeight + $this->iMargin),
            mt_rand(1, $this->iHeight + $this->iMargin),
            mt_rand(1, $this->iWidth + $this->iMargin),
            mt_rand(2, $this->iHeight + $this->iMargin),
            $this->rRed
        );

        imageline(
            $this->rImg,
            mt_rand(2, $this->iHeight + $this->iMargin),
            mt_rand(1, $this->iWidth + $this->iMargin),
            mt_rand(1, $this->iWidth + $this->iMargin),
            mt_rand(2, $this->iHeight + $this->iMargin),
            $this->aColor[array_rand($this->aColor)]
        );

        unset($this->rBlack, $this->rRed, $this->rWhite);


        imageconvolution($this->rImg, self::$aMatrixBlur, 9, 0);
        imageconvolution($this->rImg, self::$aMatrixBlur, 9, 0);

        (new Browser)->noCache();
        header('Content-type: image/png');
        imagepng($this->rImg);
        imagedestroy($this->rImg);
    }

    /**
     * @param string $sCode The random code.
     * @param bool $bIsCaseSensitive
     *
     * @return bool
     */
    public function check($sCode, $bIsCaseSensitive = true)
    {
        if ($sCode === null) {
            return false;
        }

        $sUserInput = $this->oSession->get(self::SESSION_NAME);

        if (!$bIsCaseSensitive) {
            $sCode = strtolower($sCode);
            $sUserInput = strtolower($sUserInput);
        }

        if ($sCode === $sUserInput) {
            return true;
        }

        return false;
    }

    /**
     * The HTML code for displaying the captcha.
     *
     * @return void
     */
    public function display()
    {
        // Md5 parameter in the img tag to the captcha that the browser does not cache the captcha
        echo // The captcha stylesheet that is now in the file form.css
        '<div class="center">
           <img class="border captcha" src="', PH7_URL_ROOT, 'asset/file/captcha/?r=', md5(time()), '" id="captcha" alt="Captcha Image" />
           <a class="captcha_button" href="#" onclick="document.getElementById(\'captcha\').src =\'', PH7_URL_ROOT, 'asset/file/captcha/?r=\' + Math.random(); return false"><img src="', PH7_URL_STATIC, PH7_IMG, 'icon/', self::RELOAD_ICON_FILENAME, '" onclick="this.blur()" id="refresh" alt="Refresh Image" title="Refresh Image" /></a>
         </div>';
    }

    /**
     * @return void
     */
    private function mixing()
    {
        for ($i = 0, $iLength = strlen($this->sStr); $i < $iLength; ++$i) {
            $sText = $this->sStr[$i]; // A string can be seen as an array
            $iAngle = mt_rand(-70, 70);

            imagettftext(
                $this->rImg,
                mt_rand($this->iSize / 2, $this->iSize),
                $iAngle,
                ($i * $this->iStringWidth) + $this->iMargin,
                $this->iHeight + mt_rand(1, $this->iMargin / 2),
                $this->aColor[array_rand($this->aColor)],
                $this->sFont,
                $sText
            );
        }
    }

    /**
     * @param int $iStringLength
     *
     * @return int
     */
    private function getCorrectStringLength($iStringLength)
    {
        if ($iStringLength < self::NUM_CHARACTER_CAPTCHA) {
            return self::NUM_CHARACTER_CAPTCHA;
        }

        if ($iStringLength > self::NAX_CHARACTER_CAPTCHA) {
            return self::NAX_CHARACTER_CAPTCHA;
        }

        return $iStringLength;
    }

    /**
     * @return string The font path of captcha.
     */
    private function getFont()
    {
        //$count = count(glob(PH7_PATH_DATA . '/font/*.ttf'));
        //return PH7_PATH_DATA . '/font/' . mt_rand(1,$count) . '.ttf';
        return PH7_PATH_DATA . '/font/4.ttf';
    }
}
