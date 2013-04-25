<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Router\UriRoute, PH7\Framework\Url\HeaderUrl;

class AdsAdminFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        (new AdsCoreModel)->add($_POST['title'], $_POST['code'], 'AdsAffiliate');

        /* Clean Model\Design for STATIC data */
        (new Framework\Cache\Cache)->start(Framework\Mvc\Model\Design::CACHE_STATIC_GROUP, null, null)->clear();

        HeaderUrl::redirect(UriRoute::get('affiliate', 'admin', 'ads'), t('The Advertisements was added successfully!'));
    }

}
