<?php
/**
 * @title            Annotation Class
 * @desc             This class makes use of annotations (similar to Java >= 5).
 *                   To use annotations, you must inherit your class with this Annotation class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Annotation
 * @version          1.1
 */

namespace PH7\Framework\Annotation;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\CArray\CArray;
use ReflectionClass;

abstract class Annotation
{
    const REGEX_COMMENT = '/\/\*\*(.*)\*\//';
    const REGEX_NEWLINE = '/\n/';

    const CACHE_GROUP = 'str/annotation';

    /** @var Cache */
    private $oCache;

    /**
     * Get an Annotation.
     *
     * @param string $sName The name of the annotation.
     *
     * @return string|null The value annotation. If the annotation name is not found, Returns NULL.
     */
    public function getAnnotation($sName)
    {
        $aAnnotations = $this->getAnnotations();

        return CArray::getValueByKey($sName, $aAnnotations);
    }

    /**
     * Get the annotations.
     *
     * @return array
     */
    public function getAnnotations()
    {
        $sClassName = get_class($this);

        $this->initializeAnnotations($sClassName);

        if (!$aSchema = $this->oCache->get()) {
            $aClassVars = get_class_vars($sClassName);

            $oReflection = new ReflectionClass($this);

            $aSchema = [];

            foreach ($aClassVars as $sName => $sValue) {
                $oProperty = $oReflection->getProperty($sName);
                $sComment = $oProperty->getDocComment();

                $sComment = preg_replace(self::REGEX_COMMENT, '$1', $sComment);
                $aComment = preg_split(self::REGEX_NEWLINE, $sComment);

                $sKey = $sVal = null; // Set default values
                $aSchema[$sName] = array();

                foreach ($aComment as $sCommentLine) {
                    if (preg_match('/@(.*?): (.*)/i', $sCommentLine, $aMatches)) {
                        $sKey = $aMatches[1];
                        $sVal = $aMatches[2];

                        $aSchema[$sName][trim($sKey)] = trim($sVal);
                    }
                }
            }

            unset($oReflection);

            $this->saveAnnotations($aSchema);
        }

        return $aSchema;
    }

    /**
     * Initialize Cache Annotations.
     *
     * @param string $sClassName
     */
    protected function initializeAnnotations($sClassName)
    {
        $this->oCache = (new Cache)->start(
            static::CACHE_GROUP,
            $sClassName,
            null // The last parameter is NULL, then the cache will never expire
        );
    }

    /**
     * Save the Annotations in the cache.
     *
     * @param array $aAnnotations
     *
     * @return void
     */
    protected function saveAnnotations(array $aAnnotations)
    {
        $this->oCache->put($aAnnotations);
    }
}
