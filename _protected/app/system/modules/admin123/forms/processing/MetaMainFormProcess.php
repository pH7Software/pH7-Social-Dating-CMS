<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use stdClass;

class MetaMainFormProcess extends Form
{
    /** @var array */
    private static $aMetaFields = [
        'page_title' => 'pageTitle',
        'headline' => 'headline',
        'slogan' => 'slogan',
        'promo_text' => 'promoText',
        'meta_description' => 'metaDescription',
        'meta_keywords' => 'metaKeywords',
        'meta_robots' => 'metaRobots',
        'meta_author' => 'metaAuthor',
        'meta_copyright' => 'metaCopyright',
        'meta_rating' => 'metaRating',
        'meta_distribution' => 'metaDistribution',
        'meta_category' => 'metaCategory'
    ];

    public function __construct()
    {
        parent::__construct();

        $oMetaData = DbConfig::getMetaMain($this->httpRequest->get('meta_lang'));
        $this->updateFields($oMetaData);

        DbConfig::clearCache();

        \PFBC\Form::setSuccess('form_meta', t('Meta Tags successfully updated!'));
    }

    /**
     * Update the fields in the DB (if modified only).
     *
     * @param stdClass $oMeta Meta Main DB data.
     */
    private function updateFields(stdClass $oMeta)
    {
        foreach (self::$aMetaFields as $sKey => $sVal) {
            if (!$this->str->equals($this->httpRequest->post($sKey), $oMeta->$sVal)) {
                $sParam = ($sKey == 'promo_text') ? Http::ONLY_XSS_CLEAN : null;
                DbConfig::setMetaMain($sVal, $this->httpRequest->post($sKey, $sParam), $oMeta->langId);
            }
        }
    }
}
