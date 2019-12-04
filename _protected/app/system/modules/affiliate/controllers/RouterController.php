<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

use PH7\Framework\Url\Header;

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
            $this->addReferer();
        }

        $this->redirectToWebsite();
    }

    private function addReferer()
    {
        $sUsername = $this->httpRequest->get('aff');

        if ((new ExistsCoreModel)->username($sUsername, DbTableName::AFFILIATE)) {
            (new Affiliate)->addRefer($sUsername);
        }
    }

    /**
     * Redirect the user to the website's homepage or a specific page if GET 'action' is specified.
     *
     * @return void
     */
    private function redirectToWebsite()
    {
        $sUrl = $this->registry->site_url . $this->httpRequest->get('action');
        Header::redirect($sUrl);
    }
}
