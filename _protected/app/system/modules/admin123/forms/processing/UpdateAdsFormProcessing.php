<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\HttpRequest;

class UpdateAdsFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $sTable = AdsCore::getTable();
        (new AdsCoreModel)->update($this->httpRequest->post('id_ads'), $this->httpRequest->post('title'), $this->httpRequest->post('code', HttpRequest::NO_CLEAN), $sTable);

        /* Clean DesignModel for STATIC data */
        (new Framework\Cache\Cache)->start(Framework\Mvc\Model\DesignModel::CACHE_STATIC_GROUP, null, null)->clear();

        \PFBC\Form::setSuccess('form_update_ads', t('The Advertisements was saved successfully!'));
    }

}
