<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Report / Controller
 */

declare(strict_types=1);

namespace PH7;

class MainController extends Controller
{
    public function abuse(): void
    {
        $this->view->page_title = t('Report Abuse/Content');

        $this->output();
    }
}
