<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7builder.com
 * @link           http://github.com/pH7Software/pH7Builder-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Api\AllowCors;
use PH7\Framework\Api\Api;
use PH7\Framework\Http\Rest\Rest;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\Version;
use PH7\JustHttp\StatusCode;

class MainController extends Controller
{
    use Api; // Import the Api Trait

    protected Rest $oRest;

    public function __construct()
    {
        parent::__construct();

        $this->oRest = new Rest;
        $this->setCorsHeaders();
    }

    /**
     * Test if the API is responding.
     */
    public function ping(): void
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
     */
    public function info(): void
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

    private function setCorsHeaders(): void
    {
        $oCors = new AllowCors();
        $oCors->init();
        unset($oCors);
    }
}
