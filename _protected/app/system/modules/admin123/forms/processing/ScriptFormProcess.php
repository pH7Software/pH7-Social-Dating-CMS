<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Request\Http, PH7\Framework\Mvc\Model\Design;

class ScriptFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->str->equals($this->httpRequest->post('code', Http::NO_CLEAN), (new Design)->customCode('js')))
        {
            (new AdminModel)->updateCustomCode($this->httpRequest->post('code', Http::NO_CLEAN), 'js');

            /* Clean Model\Design for STATIC / customCodejs data */
            (new Framework\Cache\Cache)->start(Design::CACHE_STATIC_GROUP, 'customCodejs', null)->clear();
        }
        \PFBC\Form::setSuccess('form_script', t('The JS code has been successfully updated!'));
    }

}
