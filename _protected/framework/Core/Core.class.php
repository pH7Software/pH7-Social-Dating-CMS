<?php
/**
 * @title            Core Class
 * @desc             Core Class of the pH7CMS.
 *
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Core
 */

namespace PH7\Framework\Core;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly as CurlySyntax;
use PH7\Framework\Session\Session;

abstract class Core extends Kernel
{
    /** @var Session */
    protected $session;

    /** @var Design */
    protected $design;

    /** @var CDateTime */
    protected $dateTime;

    /** @var PH7Tpl */
    protected $view;

    public function __construct()
    {
        parent::__construct();

        $this->session = new Session;
        $this->design = new Design;
        $this->dateTime = new CDateTime;
        $this->view = new PH7Tpl(new CurlySyntax);
    }
}
