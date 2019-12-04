<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Request\Http;

class StyleFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sCode = $this->httpRequest->post('code', Http::NO_CLEAN);

        if (!$this->str->equals($sCode, (new Design)->customCode('css'))) {
            (new AdminModel)->updateCustomCode($sCode, 'css');

            $this->clearCache();
        }
        \PFBC\Form::setSuccess('form_style', t('The CSS code has been updated successfully!'));
    }

    private function clearCache()
    {
        (new Cache)->start(Design::CACHE_STATIC_GROUP, 'customCodecss', null)->clear();
    }
}
