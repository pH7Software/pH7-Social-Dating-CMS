<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7cms.com
 * @link           http://github.com/pH7Software/pH7CMS-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Api\AllowCors;
use PH7\Framework\Api\Api;
use PH7\Framework\Http\Rest\Rest;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\Version;
use Teapot\StatusCode;

class MainController extends Controller
{
    use Api; // Import the Api Trait

    /** @var Rest */
    protected $oRest;

    public function __construct()
    {
        parent::__construct();

        $this->oRest = new Rest;
        $this->setCorsHeaders();
    }

    /**
     * Test if the API is responding.
     *
     * @return void
     */
    public function ping()
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_GET) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $this->oRest->response($this->set(['return' => 'Pong']));
        }
    }

    /**
     * Gives software information
     * (such as software name, website URL, version number, version name, etc).
     *
     * @return void
     */
    public function info()
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_GET) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $aInfo = [
                'software' => [
                    'name' => self::SOFTWARE_NAME,
                    'url' => self::SOFTWARE_WEBSITE,
                    'github' => self::SOFTWARE_GIT_REPO_URL,
                    'version' => [
                        'number' => Version::KERNEL_VERSION,
                        'build' => Version::KERNEL_BUILD,
                        'name' => Version::KERNEL_VERSION_NAME,
                        'date' => Version::KERNEL_RELEASE_DATE
                    ]
                ]
            ];

            $this->oRest->response($this->set($aInfo));
        }
    }

    private function setCorsHeaders()
    {
        $oCors = new AllowCors();
        $oCors->init();
        unset($oCors);
    }
}
