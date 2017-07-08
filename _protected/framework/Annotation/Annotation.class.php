<?php
/**
 * @title            Annotation Class
 * @desc             This class makes use of annotations (similar to Java >= 5).
 *                   To use annotations, you must inherit your class with this Annotation class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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
    const CACHE_GROUP = 'str/annotation';

    /**
     * @var object $oCache
     */
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

        if (!$aChema = $this->oCache->get()) {
            $aClassVars = get_class_vars($sClassName);

            $oReflection = new ReflectionClass($this);

            $aChema = array();

            foreach ($aClassVars as $sName => $sValue) {
                $oProperty = $oReflection->getProperty($sName);
                $sComment = $oProperty->getDocComment();

                $sComment = preg_replace('/\/\*\*(.*)\*\//', '$1', $sComment);
                $aComment = preg_split('/\n/', $sComment);

                $sKey = $sVal = null;
                $aChema[$sName] = array();

                foreach ($aComment as $sCommentLine) {
                    if (preg_match('/@(.*?): (.*)/i', $sCommentLine, $aMatches)) {
                        $sKey = $aMatches[1];
                        $sKey = $aMatches[2];

                        $aChema[$sName][trim($sKey)] = trim($sVal);
                    }
                }
            }

            unset($oReflection);

            $this->saveAnnotations($aChema);
        }

        return $aChema;
    }

    /**
     * Initialize Cache Annotations.
     *
     * @param string $sClassName
     */
    protected function initializeAnnotations($sClassName)
    {
        $this->oCache = (new Cache)->start(static::CACHE_GROUP, $sClassName, null); // The last parameter is NULL, then the cache will never expire
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
