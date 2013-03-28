<?php
/**
 * @title            String Class
 * @desc             Many useful functions for string manipulation.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Str
 * @version          1.1
 */

namespace PH7\Framework\Str {
defined('PH7') or exit('Restricted access');

 class Str
 {

     /**
      * @desc Make a string lowercase.
      * @param string $sText
      * @return string
      */
     public function lower($sText)
     {
         return mb_strtolower($sText);
     }

     /**
      * @desc Make a string uppercase.
      * @param string $sText
      * @return string
      */
     public function upper($sText)
     {
         return mb_strtoupper($sText);
     }

     /**
      * @desc Make a string's first character lowercase.
      * @param string $sText
      * @return string
      */
     public function lowerFirst($sText)
     {
         return lcfirst($sText);
     }

     /**
      * @desc Make a string's first character uppercase.
      * @param string $sText
      * @return string
      */
     public function upperFirst($sText)
     {
         return ucfirst($sText);
     }

     /**
      * @desc Uppercase the first character of each word in a string.
      * @param string $sText
      * @return string
      */
     public function upperFirstWords($sText)
     {
         return ucwords($sText);
     }

     /**
      * @desc Count the length of a string and supports the special characters (Asian, Latin, ...).
      * @param string $sText
      * @return string
      */
     public function length($sText)
     {
         return mb_strlen($sText, PH7_ENCODING);
     }

     /**
      * @desc String sanitize.
      * @param string $sText
      * @param string $sFilter Optionally, The some strings separated by a comma.
      * @param string $sFlag Optionally, a flag
      * @return string
      *
      */
     public function sanitize($sText, $sFilter = null, $sFlag = null)
     {
         $sFlag = (!empty($sFlag)) ? (string) $sFlag : '';

         if (!empty($sFilter))
         {
             $aFilters = explode(',', $sFilter);
             foreach ($aFilters as $sF)
                 $sText = str_replace($sF, $sFlag, $sText);
         }

         $sText = preg_replace('/[\r\n\t]+/', '', $sText); // Remove new lines, spaces, tabs
         $sText = preg_replace('/>[\s]+</', '><', $sText); // Remove new lines, spaces, tabs
         $sText = preg_replace('/[\s]+/', ' ', $sText); // Remove new lines, spaces, tabs

         return $sText;
     }

     /**
      * @desc Test the equality of two strings.
      * @personal For the PHP AND C functions, strcmp and strcasecmp returns a positive or negative integer value if they are different and 0 if they are equal.
      * @param string $text1
      * @param string $text2
      * @return boolean
      */
     public function equals($sText1, $sText2)
     {
         //return (strcmp($sText1, $sText2) === 0) ? true : false;
         return ($sText1 === $sText2);
     }

     /**
      * @desc Equals but not case sensitive. Accepts uppercase and lowercase.
      * @param string $sText1
      * @param string $sText2
      * @return boolean
      */
     public function equalsIgnoreCase($sText1, $sText2)
     {
         //return (strcasecmp($sText1, $sText2) === 0) ? true : false;
         $sText1 = static::lower($sText1);
         $sText2 = static::lower($sText2);
         return static::equals($sText1, $sText2);
     }

     /*
      * @desc Creates a new string by trimming any leading or trailing whitespace from the current string.
      * @param string $sText
      * @return string
      */
     public function trim($sText)
     {
         return trim($sText);
     }

     /**
      * @desc Cut a piece of string to make an extract.
      * @param string $sText
      * @param integer $iStart Default 0
      * @param integer $iLength Default 150
      * @param string $sTrimMarker Default '...'
      * @return string
      */
     public function extract($sText, $iStart = 0, $iLength = 150, $sTrimMarker = '...')
     {
         if (function_exists('mb_strimwidth'))
         {
             $sText = mb_strimwidth($sText, $iStart, $iLength, $sTrimMarker, PH7_ENCODING);
         }
         else
         {
             // Recovers a portion of our content.
             $sExtract = substr($sText, $iStart, $iLength);

             // Find the last space after the last word of the extract.
             if ($iLastSpace = strrpos($sExtract, ' '))
                 // Cut the chain to the last space.
                 $sText = substr($sText, $iStart, $iLastSpace);
             else
                 // If the string contains any spaces, we cut the chain with the maximum number of characters given.
                 $sText = substr($sText, $iStart, $iLength);

             $sText .= $sTrimMarker;
         }

         return $sText;
     }

     /**
      * @desc Return the string if the variable is not empty else return empty string.
      * @param string $sText
      * @return string
      */
     public function get($sText)
     {
         return (!empty($sText)) ? $sText : '';
     }

     /*
      * @desc Escape function, uses the PHP native htmlspecialchars but improves.
      * @param mixed (array or string) $mText
      * @param boolean $bStrip Default: FALSE
      * @return mixed (array or string) content to HTML entities.
      */
     public function escape($mText, $bStrip = false)
     {
         return (is_array($mText)) ? $this->arrayEscape($mText, $bStrip) : $this->CEscape($mText, $bStrip);
     }

     /**
      * @desc Escape an array of any dimension.
      * @access protected
      * @param array $aData
      * @param boolean $bStrip
      * @return array The array escaped.
      */
     protected function arrayEscape(array $aData, $bStrip)
     {
         foreach ($aData as $sKey => $mValue)
             $aData[$sKey] = (is_array($mValue)) ? $this->arrayEscape($mValue, $bStrip) : $this->CEscape($mValue, $bStrip);

         return $aData;
     }

     /**
      * @access protected
      * @param string $sText
      * @param boolean $bStrip
      * @return The text parsed by Str::stripTags() method if $bStrip parameter is true otherwise by Str::htmlSpecialChars method.
      */
     protected function CEscape($sText, $bStrip)
     {
         return (true === $bStrip) ? $this->stripTags($sText) : $this->htmlSpecialChars($sText);
     }

     /**
      * @access protected
      * @param string $sText
      * @return string The text parsed by strip_tag() function
      */
     protected function stripTags($sText)
     {
         return strip_tags($sText);
     }

     /**
      * @access protected
      * @param string $sText
      * @return string The text parsed by htmlspecialchars() function
      */
     protected function htmlSpecialChars($sText)
     {
         return htmlspecialchars($sText, ENT_QUOTES, 'utf-8');
     }

 }

}

namespace {

      /**
       * @desc Alias of the \PH7\Framework\Str\Str::escape() method.
       */
    function escape($mText, $bStrip = false)
    {
        return (new PH7\Framework\Str\Str)->escape($mText, $bStrip);
    }

}
