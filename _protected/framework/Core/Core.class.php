<?php
/**
 * @title            Core Class
 * @desc             Core Class of the pH7CMS.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @link             http://software.hizup.com
 * @package          PH7 / Framework / Core
 */

namespace PH7\Framework\Core;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Session\Session,
PH7\Framework\Layout\Html\Design,
PH7\Framework\Date\CDateTime,
PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;

abstract class Core extends Kernel
{
    protected $session, $design, $dateTime, $view;

    public function __construct()
    {
        parent::__construct();

        $this->session = new Session;
        $this->design = new Design;
        $this->dateTime = new CDateTime;
        $this->view = new PH7Tpl;
    }
}
