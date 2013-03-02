<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Error / Controller
 */
namespace PH7;
use PH7\Framework\Http\Http, PH7\Framework\Mvc\Router\UriRoute;

class HttpController extends Controller
{

    public function index()
    {
        switch ($this->httpRequest->get('error', 'int'))
        {
            case 401:
                Http::setHeadersByCode(401);
                $this->view->title_page = t('Unauthorized');
                $this->view->h1_title = t('Unauthorized');
                $this->view->error_desc = t('Your IP address or the username/password you entered were not correct. Your request was denied as you have no permission to access the data.');
            break;

            case 402:
                Http::setHeadersByCode(402);
                $this->view->title_page = t('Payment Required');
                $this->view->h1_title = t('Payment Required');
                $this->view->error_desc = t('The data is not accessible at the time. The owner of the space has not yet payed their service provider.');
            break;

            case 403:
                Http::setHeadersByCode(403);
                $this->view->title_page = t('Forbidden');
                $this->view->h1_title = t('Forbidden');
                $this->view->error_desc = t('Your IP address or the username/password you entered were not correct. Your request was denied as you have no permission to access the data.<br />OR<br />The server was unable to serve the data that was requested.');
            break;

            case 500:
                Http::setHeadersByCode(500);
                $this->view->title_page = t('Internal Server Error');
                $this->view->h1_title = t('Internal Server Error');
                $this->view->error_desc = t('The server encountered an error. This is most often caused by a scripting problem, a failed database access attempt, or other similar reasons.<br />Please come back later!');
            break;

            case 501:
                Http::setHeadersByCode(501);
                $this->view->h1_title = t('Not Implemented');
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

            case 504:
                Http::setHeadersByCode(504);
                $this->view->h1_title = t('Gateway Timeout');
                $this->view->error_desc = t("Most likely the client has lost connectivity (disconnected from the internet) or the client's host is having technical difficulties.<br /> This could also meanthat a server that allows access to the requested server is down, having bandwidth/load issues, or otherwise unavailable.");
            break;

            default:
                // 404 Status Code
                $this->displayPageNotFound();
        }

        $this->output();
    }

}
