<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7cms.com
 * @link           http://github.com/pH7Software/pH7CMS-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Api\Api;
use PH7\Framework\Http\Rest\Rest;
use PH7\Framework\Mvc\Request\Http as HttpRequest;

class MainController extends Controller
{
    use Api; // Import the Api Trait

    /** @var Rest */
    protected $oRest;

    public function __construct()
    {
        parent::__construct();

        $this->oRest = new Rest;
    }

    /**
     * Test if the API works well.
     *
     * @return void
     */
    public function test()
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_POST) {
            $this->oRest->response('', 406);
        } else {
            $this->oRest->response($this->set(['return' => 'It Works!']));
        }
    }
}