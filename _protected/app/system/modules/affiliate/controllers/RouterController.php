<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */
namespace PH7;

class RouterController extends Controller
{

    /**
     * Set the reference to the visitor.
     *
     * @param string $sAff Affiliate's username (his ID). Default: NULL
     * @param string $sAction Change the redirect URL. Default: ''
     * @return void
     */
    public function refer($sAff = null, $sAction = '')
    {
        if (!empty($sAff))
            if ((new ExistsCoreModel)->username($sAff, 'Affiliates'))
                (new Affiliate)->addRefer($sAff);

        Framework\Url\HeaderUrl::redirect($this->registry->site_url . $sAction);
    }

}
