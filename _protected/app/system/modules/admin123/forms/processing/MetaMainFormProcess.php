<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig, PH7\Framework\Mvc\Request\Http;

class MetaMainFormProcess extends Form
{

    private $aMetaFields = [
        'lang_id' => 'langId',
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

        $sWhereLang = $this->httpRequest->get('meta_lang');
        $oMeta = DbConfig::getMetaMain($sWhereLang);

        foreach ($this->aMetaFields as $sKey => $sVal)
            if (!$this->str->equals($this->httpRequest->post($sKey), $oMeta->langId))
                DbConfig::setMetaMain($sVal, $this->httpRequest->post($sKey), $sWhereLang);

        /* Clean DbConfig Cache */
        (new Framework\Cache\Cache)->start(DbConfig::CACHE_GROUP, null, null)->clear();

        \PFBC\Form::setSuccess('form_meta', t('The Meta Tags has been saved successfully!'));
    }

}
