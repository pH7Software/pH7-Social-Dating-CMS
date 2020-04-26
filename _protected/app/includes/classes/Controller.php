<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Controller\Controller as FwkController;
use PH7\Framework\Mvc\Model\DbConfig;

abstract class Controller extends FwkController
{
    public function __construct()
    {
        parent::__construct();

        $this->addMenuCssFile();
    }

    /**
     * Add the menu.css file (when navbar isn't set to 'dark' mode).
     *
     * @return void
     */
    protected function addMenuCssFile()
    {
        if (DbConfig::getSetting('navbarType') !== 'inverse') {
            $this->design->addCss(
                PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
                'menu.css'
            );
        }
    }
}
