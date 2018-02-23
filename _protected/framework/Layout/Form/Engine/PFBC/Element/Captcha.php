<?php
/**
 * Some changes were made by Pierre-Henry Soria
 */

namespace PFBC\Element;

use PH7\Framework\Mvc\Model\DbConfig;

class Captcha extends \PFBC\Element
{
    /**
     * @param string $sLabel
     * @param array|null $aProperties
     */
    public function __construct($sLabel = '', array $aProperties = null)
    {
        parent::__construct($sLabel, 'recaptcha_response_field', $aProperties);
    }

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Captcha(DbConfig::getSetting('recaptchaPrivateKey'));
        require_once(__DIR__ . '/../Resources/recaptchalib.php');

        echo recaptcha_get_html(DbConfig::getSetting('recaptchaPublicKey'));
    }
}
