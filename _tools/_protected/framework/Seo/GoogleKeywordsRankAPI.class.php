<?php

namespace PH7\Framework\Seo;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7Exception;

/*
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
*  @author         CERDAN Yohann <cerdanyohann@yahoo.fr>
*  @copyright      (c) 2009  CERDAN Yohann, All rights reserved
*  @version       19:38 04/08/2009
*
* Modified by Pierre-Henry SORIA <hi@ph7.me>
*/

class GoogleKeywordsRankAPI
{
    /** URL of the website to check in the google results **/
    private $url = '';

    /** Max number of pages of google to parse (there is 10 results per page) **/
    private $maxPages = 1;

    /** Extension of the google domain (fr,com,...) **/
    private $extension = 'com';

    /** The HTML response send by the service **/
    private $response;

    /**
     * Class constructor
     *
     * @param string $url the url of the website
     *
     * @return void
     */

    public function __construct($url, $maxPages = 1, $extension = 'com')
    {
        $this->url = str_replace('http://www.', '', $url);
        $this->extension = $extension;

        $this->maxPages = 1;
        if ($maxPages > 0) {
            $this->maxPages = $maxPages;
        }
    }

    /**
     * Set the max number of pages of google to parse
     *
     * @param int $maxPages the max number of pages
     *
     * @return void
     */
    public function setMaxPages($maxPages)
    {
        $this->maxPages = 1;
        if ($maxPages > 0) {
            $this->maxPages = $maxPages;
        }
    }

    /**
     * Get the max number of pages of google to parse
     *
     * @return int maxPages
     */
    public function getMaxPages()
    {
        return $this->maxPages;
    }

    /**
     * Get URL content using cURL.
     *
     * @param string $url the url
     *
     * @return string the html code
     */
    public function getContent($url)
    {
        if (!extension_loaded('curl')) {
            throw new PH7Exception('curl extension is not available');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $this->response = curl_exec($curl);
        $infos = curl_getinfo($curl);
        curl_close($curl);
        return $infos['http_code'];
    }

    /**
     * Get the position of the keywords
     *
     * @param string $keywords keywords
     *
     * @return array An array with keywords=>rank
     */
    public function getKeywordsRank($keywords)
    {
        if (isset($this->url) && isset($keywords)) {
            $base_url = 'http://www.google.' . $this->extension . '/search?q=' . urlencode($keywords) . '&start=';

            $index = 0; // counting start from here
            $page = 0;

            for ($page = 0; $page < $this->maxPages; $page++) {

                $make_url = $base_url . ($page * 10);

                $getContentCode = $this->getContent($make_url);

                if ($getContentCode == 200) {

                    if (preg_match_all('/a href="([^"]+)" class=l.+?>.+?<\/a>/', $this->response, $results) > 0) {
                        foreach ($results[1] as $link) {
                            $link = preg_replace('(^http://|/$)', '', $link);
                            $index++;
                            if (strlen(stristr($link, $this->url)) > 0) {
                                return array($keywords, $index);
                            }
                        }
                    } else {
                        trigger_error('Google results parse problem : could not find the html result code ', E_USER_WARNING);
                        return null;
                    }

                } else {
                    trigger_error('Google results parse problem : http error ' . $getContentCode, E_USER_WARNING);
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Get the position of the keywords array
     * There is a sleep() function of 3 seconds because google can ban you for 24 hours if the number of search is too large(1000 req/24 hour)
     *
     * @param array $keywords array of keywords
     *
     * @return array arrays with keywords=>rank
     */
    public function getKeywordsArrayRank($keywords)
    {
        $keywords_rank = array();

        foreach ($keywords as $keyword) {
            $rank = $this->getKeywordsRank($keyword);

            $keywords_rank [] = array($keyword, 0);
            if ($rank) {
                $keywords_rank [] = $rank;
            }

            sleep(3);
        }

        return $keywords_rank;
    }
}
