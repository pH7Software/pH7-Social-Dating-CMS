<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Error / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;

class HttpController extends Controller
{

    private $sTitle;

    public function index()
    {
        switch ($this->httpRequest->get('code', 'int')) {
            case 400:
                Http::setHeadersByCode(400);
                $this->sTitle = t('Bad Request');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The request cannot be fulfilled due to bad syntax.');
                break;

            case 401:
                Http::setHeadersByCode(401);
                $this->sTitle = t('Unauthorized');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('Your IP address or the username/password you entered were not correct. Your request was denied as you have no permission to access the data.');
                break;

            case 402:
                Http::setHeadersByCode(402);
                $this->sTitle = t('Payment Required');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The data is not accessible at the time. The owner of the space has not yet payed their service provider.');
                break;

            case 403:
                Http::setHeadersByCode(403);
                $this->sTitle = t('Forbidden');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('Your IP address or the username/password you entered were not correct. Your request was denied as you have no permission to access the data.<br />OR<br />The server was unable to serve the data that was requested.');
                break;

            case 405:
                Http::setHeadersByCode(405);
                $this->sTitle = t('Method Not Allowed');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('A request was made of a resource using a request method not supported by that resource;[2] for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.');
                break;

            case 500:
                Http::setHeadersByCode(500);
                $this->sTitle = t('Internal Server Error');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server encountered an error. This is most often caused by a scripting problem, a failed database access attempt, or other similar reasons.<br />Please come back later!');
                break;

            case 501:
                Http::setHeadersByCode(501);
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

            case 502:
                Http::setHeadersByCode(502);
                $this->sTitle = t('Bad Gateway');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server was acting as a gateway or proxy and received an invalid response from the upstream server.');
                break;

            case 504:
                Http::setHeadersByCode(504);
                $this->sTitle = t('Gateway Timeout');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t("Most likely the client has lost connectivity (disconnected from the internet) or the client's host is having technical difficulties.<br /> This could also meanthat a server that allows access to the requested server is down, having bandwidth/load issues, or otherwise unavailable.");
                break;

            case 505:
                Http::setHeadersByCode(505);
                $this->sTitle = t('HTTP Version Not Supported');
                $this->view->page_title = $this->sTitle;
                $this->view->h1_title = $this->sTitle;
                $this->view->error_desc = t('The server does not support the HTTP protocol version used in the request.');
                break;

            default:
                // Add an image for 404 not found page, so we need to include a stylesheet file
                $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'style.css');

                $this->displayPageNotFound();
        }

        $this->output();
    }

}
