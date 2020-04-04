<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Http\Http;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Security\CSRF\Token;
use Teapot\StatusCode;

class CacheAjax extends Kernel
{
    public function __construct()
    {
        parent::__construct();

        if (!(new Token)->check('cache')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        $this->clearCache();
    }

    private function clearCache()
    {
        switch ($this->httpRequest->post('type')) {
            case 'general':
                $this->file->deleteDir(PH7_PATH_CACHE . Cache::CACHE_DIR);
                $this->clearBrowserCache();
                break;

            case 'tpl_compile':
                $this->file->deleteDir(PH7_PATH_CACHE . PH7Tpl::COMPILE_DIR);
                $this->clearBrowserCache();
                break;

            case 'tpl_html':
                $this->file->deleteDir(PH7_PATH_CACHE . PH7Tpl::CACHE_DIR);
                $this->clearBrowserCache();
                break;

            case 'static':
                $this->file->deleteDir(PH7_PATH_CACHE . Gzip::CACHE_DIR);
                $this->clearBrowserCache();
                break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error');
        }

        echo jsonMsg(1, t('The cache has been cleared.'));
    }

    /**
     * Clear the Web browser's cache.
     *
     * @return void
     */
    private function clearBrowserCache()
    {
        $this->browser->noCache();
    }
}

// Only for Admins
if (Admin::auth()) {
    new CacheAjax;
}
