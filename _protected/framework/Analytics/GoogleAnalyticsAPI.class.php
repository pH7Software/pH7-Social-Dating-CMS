<?php

namespace PH7\Framework\Analytics;

defined('PH7') or exit('Restricted access');

/*
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
*  @author        CERDAN Yohann <cerdanyohann@yahoo.fr>
*  @copyright     (c) 2009  CERDAN Yohann, All rights reserved
*  @version       30/04/2011
*
* MODIFIED BY:
* @author         Pierre-Henry Soria <hello@ph7cms.com>
* @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
*/

class GoogleAnalyticsAPI
{
    /** Google account login (email) **/
    private $login;

    /** Google account password **/
    private $password;

    /** The login token to the google analytics service **/
    private $loginToken;

    /** The XML response send by the service **/
    private $response;

    /** Google analytics website ID (avalaible on your google analytics account url) **/
    private $ids;

    /** Sort the results **/
    private $sort;

    /** The param to sort (metrics or dimensions) **/
    private $sortParam;

    /** The filters query string parameter restricts the data returned from your request to the Analytics servers **/
    private $filters;

    /** Begin date of the displaying datas **/
    private $dateBegin;

    /** End date of the displaying datas **/
    private $dateEnd;

    /** The start index of the results to display (default=1) **/
    private $startIndex;

    /** The number of max results to display (default=1000) **/
    private $maxResults;


    /**
     * Class constructor
     *
     * @param string $login the login (email)
     * @param string $password the password
     * @param string $ids the IDs of the website (find it in the google analytics gui)
     * @param string $dateBegin the begin date
     * @param string $dateEnd the end date
     *
     * @return void
     */
    public function __construct($login, $password, $ids, $dateBegin, $dateEnd = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->ids = $ids;
        $this->dateBegin = $dateBegin;

        if (!$dateEnd) {
            $this->dateEnd = $dateBegin;
        } else {
            $this->dateEnd = $dateEnd;
        }

        $this->sort = "-";
        $this->sortParam = "metrics";
        $this->maxResults = 0;
        $this->startIndex = 0;

        // Authentication
        $this->login();
    }

    /**
     * Set the result's sort by metrics
     *
     * @param boolean $sort asc or desc sort
     *
     * @return void
     */
    public function setSortByMetrics($sort)
    {
        if ($sort == true) {
            $this->sort = "";
        } else {
            $this->sort = "-";
        }
        $this->sortParam = 'metrics';
    }

    /**
     * Set the result's sort by dimensions
     *
     * @param boolean $sort asc or desc sort
     *
     * @return void
     */
    public function setSortByDimensions($sort)
    {
        if ($sort == true) {
            $this->sort = "";
        } else {
            $this->sort = "-";
        }
        $this->sortParam = 'dimensions';
    }

    /**
     * Set the IDs of the website
     *
     * @param string $ids the IDs of the website (find it in the google analytics gui)
     *
     * @return void
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Set the max number of results
     *
     * @param int $maxResults the number of max results to display
     *
     * @return void
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * Set the start index of the results to display
     *
     * @param int $startIndex the start index of the results to display
     *
     * @return void
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }

    /**
     * Set the filters query string parameter restricts the data returned from your request to the Analytics servers
     *
     * @param int $filter the filters query string parameter restricts the data returned from your request to the Analytics servers
     *
     * @return void
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Set the date of the export
     *
     * @param string $dateBegin the begin date
     * @param string $dateEnd the end date
     *
     * @return void
     */
    public function setDate($dateBegin, $dateEnd = null)
    {
        $this->dateBegin = $dateBegin;

        if (!$dateEnd) {
            $this->dateEnd = $dateBegin;
        } else {
            $this->dateEnd = $dateEnd;
        }
    }

    public function getAccounts()
    {
        $url = 'https://www.google.com/analytics/feeds/accounts/default?';

        if ($this->maxResults > 0) {
            $url .= '&max-results=' . $this->maxResults;
        }

        if ($this->startIndex > 0) {
            $url .= '&start-index=' . $this->startIndex;
        }

        if ($this->getContent($url) == 200) {
            $XML_object = simplexml_load_string($this->response);
            $datas = array();
            foreach ($XML_object->entry as $m) {
                $datas [] = array('id' => (string)$m->id, 'title' => (string)$m->title);
            }
            return $datas;
        } else {
            return null;
        }
    }

    /**
     * Get URL content using cURL.
     *
     * @param string $url the url
     *
     * @return string the html code
     */
    function getContent($url)
    {
        if (!extension_loaded('curl')) {
            throw new Exception('curl extension is not available');
        }

        $ch = curl_init($url);

        $header[] = 'Authorization: GoogleLogin auth=' . $this->loginToken;

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $this->response = curl_exec($ch);
        $infos = curl_getinfo($ch);
        curl_close($ch);

        return $infos['http_code'];
    }

    /**
     * Get the google analytics datas by dimensions and metrics
     * See : http://code.google.com/intl/fr/apis/analytics/docs/gdata/gdataReferenceDimensionsMetrics.html
     * For all the parameters : http://code.google.com/intl/fr-FR/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
     *
     * @param string $metrics the metrics
     * @param string $dimensions the dimensions
     *
     * @return array
     */
    public function getDimensionByMetric($metrics, $dimensions, $filters = null)
    {
        $url = 'https://www.google.com/analytics/feeds/data?ids=ga:' . $this->ids;

        $dimensions = explode(',', $dimensions);
        $url .= '&dimensions=ga:' . implode(',ga:', $dimensions);

        $metrics = explode(',', $metrics);
        $url .= '&metrics=ga:' . implode(',ga:', $metrics);

        if ($this->sortParam == 'metrics') { // sort by metrics
            $url .= '&sort=' . $this->sort . 'ga:' . $metrics[0];
        }

        if ($this->sortParam == 'dimensions') { // sort by dimensions
            $url .= '&sort=' . $this->sort . 'ga:' . $dimensions[0];
        }

        if ($filters !== null) {
            $url .= '&filters=ga:' . $filters;
        }

        $url .= '&start-date=' . $this->dateBegin;
        $url .= '&end-date=' . $this->dateEnd;

        if ($this->maxResults > 0) {
            $url .= '&max-results=' . $this->maxResults;
        }

        if ($this->startIndex > 0) {
            $url .= '&start-index=' . $this->startIndex;
        }

        if ($this->getContent($url) == 200) {
            $XML_object = simplexml_load_string($this->response);
            $labels_array = array();
            $datas_array = array();
            $datas = array();

            foreach ($XML_object->entry as $m) {
                $dxp = $m->children('http://schemas.google.com/analytics/2009');
                $metric_att = $dxp->metric->attributes();
                $dimension_att = $dxp->dimension->attributes();
                $labels_array [] = $dimension_att['value'] . ' (' . $metric_att['value'] . ')';
                $datas_array  [] = (string)$metric_att['value'];
                $datas [] = array('label' => (string)$dimension_att['value'], 'value' => (string)$metric_att['value']);
            }
            return $datas;
            return array('labels' => $labels_array, 'datas' => $datas_array);
        } else {
            return null;
        }
    }

    /**
     * Get the google analytics datas by metrics
     * See : http://code.google.com/intl/fr/apis/analytics/docs/gdata/gdataReferenceDimensionsMetrics.html
     * For all the parameters : http://code.google.com/intl/fr-FR/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
     *
     * @param string $metrics the metrics
     * @param string $uri the url of the website page (ex : /myurl/)
     *
     * @return array
     */
    public function getMetric($metrics, $filters = null)
    {
        $url = 'https://www.google.com/analytics/feeds/data?ids=ga:' . $this->ids;
        $url .= '&metrics=ga:' . $metrics;

        if ($filters !== null) {
            $url .= '&filters=ga:' . $filters;
        }

        $url .= '&start-date=' . $this->dateBegin;
        $url .= '&end-date=' . $this->dateEnd;

        if ($this->maxResults > 0) {
            $url .= '&max-results=' . $this->maxResults;
        }

        if ($this->startIndex > 0) {
            $url .= '&start-index=' . $this->startIndex;
        }

        if ($this->getContent($url) == 200) {
            $XML_object = simplexml_load_string($this->response);
            $dxp = $XML_object->entry->children('http://schemas.google.com/analytics/2009');
            if (@count($dxp) > 0) {
                $metric_att = $dxp->metric->attributes();
            }
            return $metric_att['value'] ? (string)$metric_att['value'] : 0;
        } else {
            return null;
        }
    }

    /**
     * Login to the google server
     * See : http://google-data-api.blogspot.com/2008/05/clientlogin-with-php-curl.html
     *
     * @return void
     */
    private function login()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = array('accountType' => 'GOOGLE',
            'Email' => $this->login,
            'Passwd' => $this->password,
            'source' => 'php_curl_analytics',
            'service' => 'analytics');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $hasil = curl_exec($ch);
        curl_close($ch);

        // Get the login token
        // SID=DQA...oUE
        // LSID=DQA...bbo
        // Auth=DQA...Sxq
        if (preg_match('/Auth=(.*)$/', $hasil, $matches) > 0) {
            $this->loginToken = $matches[1];
        } else {
            trigger_error('Authentication problem', E_USER_WARNING);
            return null;
        }
    }
}
