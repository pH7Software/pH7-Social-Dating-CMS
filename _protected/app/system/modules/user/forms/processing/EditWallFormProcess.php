<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditWallFormProcess extends Form
{

/*
 * This class is still under development, if you are a developer and you want to help us and join our volunteer team of developers to continue development of this module, you are welcome!
 * Please contact us by email: ph7software@gmail.com
 *
 * Thank you,
 * The developers team.
 */
    public function __construct()
    {
        parent::__construct();

        (new WallModel)->edit($this->session->get('member_id'), $this->httpRequest->post('post'), $this->dateTime->get()->dateTime('Y-m-d H:i:s'));
        Header::redirect(Uri::get('user', 'main', 'index'), t('Your message has been added successfully!'));
    }

}
