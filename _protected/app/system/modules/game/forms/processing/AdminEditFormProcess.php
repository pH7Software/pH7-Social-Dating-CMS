<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminEditFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $aData = [
            'id' => $this->httpRequest->get('id', 'int'),
            'category_id' => $this->httpRequest->post('category_id', 'int'),
            'name' => $this->httpRequest->post('name'),
            'title' => $this->httpRequest->post('title'),
            'description' => $this->httpRequest->post('description'),
            'keywords' => $this->httpRequest->post('keywords'),
        ];

        (new GameModel)->update($aData);

        Game::clearCache();

        Header::redirect(
            Uri::get('game', 'main', 'index'),
            t('The game has been successfully updated')
        );
    }
}
