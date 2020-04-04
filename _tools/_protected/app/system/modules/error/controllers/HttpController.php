<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Error / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;

class HttpController extends Controller
{
    const HTTP_BAD_REQUEST_CODE = 400;
    const HTTP_UNAUTHORIZED_CODE = 401;
    const HTTP_PAYMENT_REQUIRED_CODE = 402;
    const HTTP_FORBIDDEN_CODE = 403;
    const HTTP_NOT_FOUND_CODE = 404;
    const HTTP_METHOD_NOT_ALLOWED_CODE = 405;
    const HTTP_INTERNAL_SERVER_ERROR_CODE = 500;
    const HTTP_UNIMPLEMENTED_CODE = 501;
    const HTTP_BAD_GATEWAY_CODE = 502;
    const HTTP_GATEWAY_TIMEOUT_CODE = 504;
    const HTTP_VERSION_UNSUPPORTED_CODE = 505;

    /** @var string */
    private $sTitle;

    public function index()
    {
        switch ($this->httpRequest->get('code', 'int')) {
            case self::HTTP_BAD_REQUEST_CODE:
                Http::setHeadersByCode(self::HTTP_BAD_REQUEST_CODE);
                $this->sTitle = t('Bad Request');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The request cannot be fulfilled due to bad syntax.');
                break;

            case self::HTTP_UNAUTHORIZED_CODE:
                Http::setHeadersByCode(self::HTTP_UNAUTHORIZED_CODE);
                $this->sTitle = t('Unauthorized');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('Your IP address or the username/password you entered were not correct. Your request was denied as you have no permission to access the data.');
                break;

            case self::HTTP_PAYMENT_REQUIRED_CODE:
                Http::setHeadersByCode(self::HTTP_PAYMENT_REQUIRED_CODE);
                $this->sTitle = t('Payment Required');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The data is not accessible at the time. The owner of the space has not yet payed their service provider.');
                break;

            case self::HTTP_FORBIDDEN_CODE:
                Http::setHeadersByCode(self::HTTP_FORBIDDEN_CODE);
                $this->sTitle = t('Forbidden');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t("You don't have permissions to access this page at the moment.");
                break;

            case self::HTTP_METHOD_NOT_ALLOWED_CODE:
                Http::setHeadersByCode(self::HTTP_METHOD_NOT_ALLOWED_CODE);
                $this->sTitle = t('Method Not Allowed');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('A request was made of a resource using a request method not supported by that resource;[2] for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.');
                break;

            case self::HTTP_INTERNAL_SERVER_ERROR_CODE:
                Http::setHeadersByCode(self::HTTP_INTERNAL_SERVER_ERROR_CODE);
                $this->sTitle = t('Internal Server Error');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server encountered an error. This is most often caused by a scripting problem, a failed database access attempt, or other similar reasons.<br />Please come back later!');
                break;

            case self::HTTP_UNIMPLEMENTED_CODE:
                Http::setHeadersByCode(self::HTTP_UNIMPLEMENTED_CODE);
                $this->sTitle = t('Not Implemented');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The method you are using to access the document can not be performed by the server.<br />Possible methods include:') .
                    '<br />' . 'CONNECT<br />
               DELETE<br />
               DELETE<br />
               GET<br />
               HEAD<br />
               OPTIONS<br />
               POST<br />
               PUT<br />
               TRACE';
                break;

            case self::HTTP_BAD_GATEWAY_CODE:
                Http::setHeadersByCode(self::HTTP_BAD_GATEWAY_CODE);
                $this->sTitle = t('Bad Gateway');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server was acting as a gateway or proxy and received an invalid response from the upstream server.');
                break;

            case self::HTTP_GATEWAY_TIMEOUT_CODE:
                Http::setHeadersByCode(self::HTTP_GATEWAY_TIMEOUT_CODE);
                $this->sTitle = t('Gateway Timeout');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t("Most likely the client has lost connectivity (disconnected from the internet) or the client's host is having technical difficulties.<br /> This could also meanthat a server that allows access to the requested server is down, having bandwidth/load issues, or otherwise unavailable.");
                break;

            case self::HTTP_VERSION_UNSUPPORTED_CODE:
                Http::setHeadersByCode(self::HTTP_VERSION_UNSUPPORTED_CODE);
                $this->sTitle = t('HTTP Version Not Supported');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server does not support the HTTP protocol version used in the request.');
                break;

            default:
                // Add an image for 404 not found page, so we need to include a stylesheet file
                $this->design->addCss(
                    PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
                    'style.css'
                );

                $this->displayPageNotFound();
        }

        $this->output();
    }
}
