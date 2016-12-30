<?php
/**
 * @title          Captcha Class
 * @desc           Generates a captcha and manages the display of the image.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / Spam / Captcha
 * @version        0.8
 */

namespace PH7\Framework\Security\Spam\Captcha;

defined('PH7') or exit('Restricted access');

use
PH7\Framework\Navigation\Browser,
PH7\Framework\Session\Session,
PH7\Framework\Util\Various;

class Captcha
{

    private
    $_oSession,
    $_sStr,
    $_sFont,
    $_iStringWidth,
    $_iHeight,
    $_iWidth,
    $_iSize = 36,
    $_iMargin = 25,
    $_aBox,
    $_aMatrixBlur = array(array(1, 1, 1), array(1, 1, 1), array(1, 1, 1)),
    $_aColor = array(),
    $_rImg,
    $_rBlack,
    $_rRed,
    $_rWhite;

    public function __construct()
    {
        $this->_oSession = new Session;
    }

    /**
     * Show the captcha image.
     *
     * @param integer $iRandom
     * @return void
     */
    public function show($iRandom = null)
    {
        if (!empty($iRandom))
            $this->_sStr = Various::genRnd($iRandom, 5);
        else
            $this->_sStr = Various::genRnd('pH7_Pierre-Henry_Soria_Sanz_González_captcha', 5);

        $this->_oSession->set('rand_code', $this->_sStr);

        $this->_sFont = $this->_getFont();
        //$sBackground = PH7_PATH_DATA . 'background/' . mt_rand(1, 5) . '.png';

        $this->_aBox = imagettfbbox($this->_iSize, 0, $this->_sFont, $this->_sStr);
        $this->_iWidth = $this->_aBox[2] - $this->_aBox[0];
        $this->_iHeight = $this->_aBox[1] - $this->_aBox[7];
        unset($this->_aBox);

        $this->_iStringWidth = round($this->_iWidth / strlen($this->_sStr));

        //$this->_rImg = imagecreatefrompng($sBackground);
        $this->_rImg = imagecreate($this->_iWidth + $this->_iMargin, $this->_iHeight + $this->_iMargin);
        $this->_aColor = array(
                          imagecolorallocate($this->_rImg, 0x99, 0x00, 0x66),
                          imagecolorallocate($this->_rImg, 0xCC, 0x00, 0x00),
                          imagecolorallocate($this->_rImg, 0x00, 0x00, 0xCC),
                          imagecolorallocate($this->_rImg, 0x00, 0x00, 0xCC),
                          imagecolorallocate($this->_rImg, 0xBB, 0x88, 0x77)
                        );
        $this->_rBlack = imagecolorallocate($this->_rImg, 0, 0, 0);
        $this->_rRed = imagecolorallocate($this->_rImg, 200, 100, 90);
        $this->_rWhite = imagecolorallocate($this->_rImg, 255, 255, 255);

        imagefilledrectangle($this->_rImg, 0, 0, 399, 99, $this->_rWhite);

        $this->_mixing();

        imageline($this->_rImg, mt_rand(2, $this->_iWidth + $this->_iMargin), mt_rand(1, $this->_iWidth + $this->_iMargin), mt_rand(1, $this->_iHeight + $this->_iMargin), mt_rand(2, $this->_iWidth + $this->_iMargin), $this->_rBlack);
        imageline($this->_rImg, mt_rand(2, $this->_iHeight + $this->_iMargin), mt_rand(1, $this->_iHeight + $this->_iMargin), mt_rand(1, $this->_iWidth + $this->_iMargin), mt_rand(2, $this->_iHeight + $this->_iMargin), $this->_rRed);
        imageline($this->_rImg, mt_rand(2, $this->_iHeight + $this->_iMargin), mt_rand(1, $this->_iWidth + $this->_iMargin), mt_rand(1, $this->_iWidth + $this->_iMargin), mt_rand(2, $this->_iHeight + $this->_iMargin), $this->_aColor[array_rand($this->_aColor)]);
        unset($this->_rBlack, $this->_rRed, $this->_rWhite);


        imageconvolution($this->_rImg, $this->_aMatrixBlur, 9, 0);
        imageconvolution($this->_rImg, $this->_aMatrixBlur, 9, 0);
        unset($this->_aMatrixBlur);

        (new Browser)->noCache();
        header('Content-type: image/png');
        imagepng($this->_rImg);
        imagedestroy($this->_rImg);
    }

    /**
     * @param string $sCode The random code.
     * @return boolean
     */
    public function check($sCode)
    {
        if ($sCode === null)
            return false;
        if ($sCode === $this->_oSession->get('rand_code'))
            return true;
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
           <a class="captcha_button" href="#" onclick="document.getElementById(\'captcha\').src =\'', PH7_URL_ROOT, 'asset/file/captcha/?r=\' + Math.random(); return false"><img src="', PH7_URL_STATIC, PH7_IMG, 'icon/reload.png" onclick="this.blur()" id="refresh" alt="Refresh Image" title="Refresh Image" /></a>
         </div>';
    }

    /**
     * @access private
     * @return void
     */
    private function _mixing()
    {
        for ($i = 0, $iLength = strlen($this->_sStr); $i < $iLength; ++$i)
        {
            $sText = $this->_sStr[$i]; // A string can be seen as an array
            $iAngle = mt_rand(-70, 70);
            imagettftext($this->_rImg, mt_rand($this->_iSize / 2, $this->_iSize), $iAngle, ($i * $this->_iStringWidth) + $this->_iMargin, $this->_iHeight + mt_rand(1, $this->_iMargin / 2), $this->_aColor[array_rand($this->_aColor)], $this->_sFont, $sText);
        }
    }

    /**
     * @access private
     * @return string The font path of captcha.
     */
    private function _getFont()
    {
        //$count = count(glob(PH7_PATH_DATA . '/font/*.ttf'));
        //return PH7_PATH_DATA . '/font/' . mt_rand(1,$count) . '.ttf';
        return PH7_PATH_DATA . '/font/4.ttf';
    }

    public function __destruct()
    {
        unset(
            $this->_oSession,
            $this->_sStr,
            $this->_sFont,
            $this->_iStringWidth,
            $this->_iHeight,
            $this->_iWidth,
            $this->_iSize,
            $this->_iMargin,
            $this->_aColor,
            $this->_rImg
        );
    }

}
