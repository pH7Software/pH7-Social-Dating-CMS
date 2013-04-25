<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Mvc\Model\Design as DesignModel;

class AnalyticsApiFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->str->equals($this->httpRequest->post('code', HttpRequest::NO_CLEAN), (new DesignModel)->analyticsApi(false, false)))
        {
            (new Framework\Mvc\Model\AnalyticsModel)->updateApi($this->httpRequest->post('code', HttpRequest::NO_CLEAN));

            /* Clean Model\Design for STATIC / analyticsApi data */
            (new Framework\Cache\Cache)->start(DesignModel::CACHE_STATIC_GROUP, 'analyticsApi', null)->clear();
        }
        \PFBC\Form::setSuccess('form_analytics_setting', t('The code Analytics Api was saved successfully!'));
    }

}
