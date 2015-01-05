<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig, PH7\Framework\Mvc\Request\Http;

class MetaMainFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $sWhereLang = $this->httpRequest->get('meta_lang');
        $oMeta = DbConfig::getMetaMain($sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('lang_id'), $oMeta->langId))
            DbConfig::setMetaMain('langId', $this->httpRequest->post('lang_id'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('page_title'), $oMeta->pageTitle))
            DbConfig::setMetaMain('pageTitle', $this->httpRequest->post('page_title'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('slogan'), $oMeta->slogan))
            DbConfig::setMetaMain('slogan', $this->httpRequest->post('slogan'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('promo_text'), $oMeta->promoText))
            DbConfig::setMetaMain('promoText', $this->httpRequest->post('promo_text', Http::ONLY_XSS_CLEAN), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_description'), $oMeta->metaDescription))
            DbConfig::setMetaMain('metaDescription', $this->httpRequest->post('meta_description'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_keywords'), $oMeta->metaKeywords))
            DbConfig::setMetaMain('metaKeywords', $this->httpRequest->post('meta_keywords'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_robots'), $oMeta->metaRobots))
            DbConfig::setMetaMain('metaRobots', $this->httpRequest->post('meta_robots'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_author'), $oMeta->metaAuthor))
            DbConfig::setMetaMain('metaAuthor', $this->httpRequest->post('meta_author'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_copyright'), $oMeta->metaCopyright))
            DbConfig::setMetaMain('metaCopyright', $this->httpRequest->post('meta_copyright'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_rating'), $oMeta->metaRating))
            DbConfig::setMetaMain('metaRating', $this->httpRequest->post('meta_rating'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_distribution'), $oMeta->metaDistribution))
            DbConfig::setMetaMain('metaDistribution', $this->httpRequest->post('meta_distribution'), $sWhereLang);

        if(!$this->str->equals($this->httpRequest->post('meta_category'), $oMeta->metaCategory))
            DbConfig::setMetaMain('metaCategory', $this->httpRequest->post('meta_category'), $sWhereLang);

        /* Clean DbConfig Cache */
        (new Framework\Cache\Cache)->start(DbConfig::CACHE_GROUP, null, null)->clear();

        \PFBC\Form::setSuccess('form_meta', t('The Meta Tags was saved successfully!'));
    }

}
