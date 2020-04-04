<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

/*
 * This class is still under development.
 * If you wish, you are more than welcome to contribute on Github: https://github.com/pH7Software/pH7-Social-Dating-CMS
 */

class EditWallFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        (new WallModel)->edit($this->session->get('member_id'), $this->httpRequest->post('post'), $this->dateTime->get()->dateTime('Y-m-d H:i:s'));
        Header::redirect(Uri::get('user', 'main', 'index'), t('Your message has been added successfully!'));
    }
}
