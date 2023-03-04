<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Controller
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

class MainController extends Controller
{
    public function error_404(): void
    {
        $this->oView->display('error_404.tpl');
    }
}
