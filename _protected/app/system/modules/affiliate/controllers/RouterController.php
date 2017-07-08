<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

class RouterController extends Controller
{
    /**
     * Set the reference to the visitor.
     *
     * @return void
     */
    public function refer()
    {
        if ($this->httpRequest->getExists('aff')) {
            if ((new ExistsCoreModel)->username($this->httpRequest->get('aff'), 'Affiliates')) {
                (new Affiliate)->addRefer($this->httpRequest->get('aff'));
            }
        }

        Framework\Url\Header::redirect($this->registry->site_url . $this->httpRequest->get('action'));
    }
}
