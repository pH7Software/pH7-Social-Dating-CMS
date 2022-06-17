<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditCategoryFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iCategoryId = $this->httpRequest->get('category_id', 'int');

        (new ForumModel)->updateCategory($iCategoryId, $this->httpRequest->post('title'));

        Header::redirect(
            Uri::get('forum', 'forum', 'index'),
            t('Category Name updated!')
        );
    }
}
