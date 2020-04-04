<?php
/**
 * @title            String Class
 * @desc             Many useful functions for string manipulation.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Str
 */

namespace PH7\Framework\Str {
    defined('PH7') or exit('Restricted access');

    class Str
    {
        const DEF_MAX_TEXT_EXTRACT_LENGTH = 150;
        const ENCODING = 'UTF-8';
        const REGEX_DELIMITER = '#';

        /**
         * Make a string lowercase.
         *
         * @param string $sText
         *
         * @return string
         */
        public function lower($sText)
        {
            return mb_strtolower($sText);
        }

        /**
         * Make a string uppercase.
         *
         * @param string $sText
         *
         * @return string
         */
        public function upper($sText)
        {
            return mb_strtoupper($sText);
        }

        /**
         * Make a string's first character lowercase.
         *
         * @param string $sText
         *
         * @return string
         */
        public function lowerFirst($sText)
        {
            return lcfirst($sText);
        }

        /**
         * Make a string's first character uppercase.
         *
         * @param string $sText
         *
         * @return string
         */
        public function upperFirst($sText)
        {
            return ucfirst($sText);
        }

        /**
         * Uppercase the first character of each word in a string.
         *
         * @param string $sText
         *
         * @return string
         */
        public function upperFirstWords($sText)
        {
            return ucwords($sText);
        }

        /**
         * Count the length of a string and supports the special characters (Asian, Latin, ...).
         *
         * @param string $sText
         *
         * @return int
         */
        public function length($sText)
        {
            return mb_strlen($sText, PH7_ENCODING);
        }

        /**
         * String sanitize.
         *
         * @param string $sText
         * @param string $sFilter Optionally, The some strings separated by a comma.
         * @param string $sFlag Optionally, a flag
         *
         * @return string
         */
        public function sanitize($sText, $sFilter = null, $sFlag = null)
        {
            $sFlag = !empty($sFlag) ? (string)$sFlag : '';

            if (!empty($sFilter)) {
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
         * Test the equality of two strings.
         *
         * @personal For the PHP AND C functions, strcmp and strcasecmp returns a positive or negative integer value if they are different and 0 if they are equal.
         *
         * @param string $sText1
         * @param string $sText2
         *
         * @return bool
         */
        public function equals($sText1, $sText2)
        {
            //return strcmp($sText1, $sText2) === 0;
            return $sText1 === $sText2;
        }

        /**
         * Equals but not case sensitive. Accepts uppercase and lowercase.
         *
         * @param string $sText1
         * @param string $sText2
         *
         * @return bool
         */
        public function equalsIgnoreCase($sText1, $sText2)
        {
            //return strcasecmp($sText1, $sText2) === 0;
            $sText1 = $this->lower($sText1);
            $sText2 = $this->lower($sText2);
            return $this->equals($sText1, $sText2);
        }

        /**
         * Find the position of the first occurrence of a specified value in a string.
         *
         * @param string $sText The string to search in.
         * @param string $sFindText Value to search.
         * @param int $iOffset Default: 0
         *
         * @return int The position of the first occurrence or -1 if the value to search is not found.
         */
        public function indexOf($sText, $sFindText, $iOffset = 0)
        {
            $mPosition = strpos($sText, $sFindText, $iOffset);

            if (!is_int($mPosition)) {
                return -1;
            }

            return $mPosition;
        }

        /**
         * Find the position of the last occurrence of a specified value in a string.
         *
         * @param string $sText The string to search in.
         * @param string $sFindText Value to search.
         * @param int $iOffset Default: 0
         *
         * @return int The position of the last occurrence or -1 if the value to search is not found.
         */
        public function lastIndexOf($sText, $sFindText, $iOffset = 0)
        {
            $mPosition = strrpos($sText, $sFindText, $iOffset);

            if (!is_int($mPosition)) {
                return -1;
            }

            return $mPosition;
        }

        /**
         * Creates a new string by trimming any leading or trailing whitespace from the current string.
         *
         * @param string $sText
         * @param string $sCharList Default: " \t\n\r\0\x0B"
         *
         * @return string
         */
        public function trim($sText, $sCharList = " \t\n\r\0\x0B")
        {
            return trim($sText, $sCharList);
        }

        /**
         * Cut a piece of string to make an extract (an ellipsis).
         *
         * @param string $sText
         * @param int $iLimit Default: 150
         * @param string $sTrimMarker Default: '...'
         *
         * @return string
         */
        public function extract($sText, $iLimit = self::DEF_MAX_TEXT_EXTRACT_LENGTH, $sTrimMarker = PH7_ELLIPSIS)
        {
            $iStart = 0;

            if ($this->length($sText) <= $iLimit) {
                return $sText;
            }

            if (function_exists('mb_strimwidth')) {
                $sText = rtrim(
                    mb_strimwidth(
                        $sText,
                        $iStart,
                        $iLimit,
                        '',
                        PH7_ENCODING
                    )
                );
            } else {
                // Recover a portion of the string
                $sExtract = substr($sText, $iStart, $iLimit);

                // Find the last space after the last word of the extract
                if ($iLastSpace = strrpos($sExtract, ' ')) {
                    // Cut the chain to the last space.
                    $sText = substr($sText, $iStart, $iLastSpace);
                } else {
                    // If the string doesn't contain any spaces, cut the string with the max number of the given characters
                    $sText = substr($sText, $iStart, $iLimit);
                }
            }

            return rtrim($sText) . $sTrimMarker;
        }

        /**
         * Return the string if the variable is not empty else return empty string.
         *
         * @param string $sText
         *
         * @return string
         */
        public function get($sText)
        {
            return !empty($sText) ? $sText : '';
        }

        /**
         * Perform a regular expression match.
         *
         * @param string $sText The string to search in.
         * @param string $sPattern The RegEx pattern to search for, as a string.
         *
         * @return string|null
         */
        public static function match($sText, $sPattern)
        {
            preg_match_all(self::regexNormalize($sPattern), $sText, $aMatches, PREG_PATTERN_ORDER);

            if (!empty($aMatches[1])) {
                return $aMatches[1];
            } elseif (!empty($aMatches[0])) {
                return $aMatches[0];
            }

            return null;
        }

        /**
         * Check if the string doesn't have any blank spaces.
         *
         * @param string $sValue
         *
         * @return bool
         */
        public static function noSpaces($sValue)
        {
            return trim($sValue) !== '';
        }

        /**
         * Escape function, uses the PHP native htmlspecialchars but improved.
         *
         * @param array|string $mText
         * @param bool $bStrip If TRUE, strip only HTML tags instead of converting them into HTML entities. Less secure. Default: FALSE
         *
         * @return array|string The escaped string.
         */
        public function escape($mText, $bStrip = false)
        {
            return is_array($mText) ? $this->arrayEscape($mText, $bStrip) : $this->cEscape($mText, $bStrip);
        }

        /**
         * Escape an array of any dimension.
         *
         * @param array $aData
         * @param bool $bStrip
         *
         * @return array The array escaped.
         */
        protected function arrayEscape(array $aData, $bStrip)
        {
            foreach ($aData as $sKey => $mValue) {
                $aData[$sKey] = is_array($mValue) ? $this->arrayEscape($mValue, $bStrip) : $this->cEscape($mValue, $bStrip);
            }

            return $aData;
        }

        /**
         * @param string $sText
         * @param bool $bStrip
         *
         * @return string The text parsed with Str::stripTags() method if $bStrip parameter is TRUE, otherwise with Str::htmlSpecialChars method.
         */
        protected function cEscape($sText, $bStrip)
        {
            return $bStrip === true ? $this->stripTags($sText) : $this->htmlSpecialChars($sText);
        }

        /**
         * @param string $sText
         *
         * @return string The text parsed with strip_tag() function
         */
        protected function stripTags($sText)
        {
            return strip_tags($sText);
        }

        /**
         * @param string $sText
         *
         * @return string The text parsed with htmlspecialchars() function
         */
        protected function htmlSpecialChars($sText)
        {
            return htmlspecialchars($sText, ENT_QUOTES, static::ENCODING);
        }

        /**
         * @param string $sPattern
         *
         * @return string
         */
        private static function regexNormalize($sPattern)
        {
            return self::REGEX_DELIMITER . trim($sPattern, self::REGEX_DELIMITER) . self::REGEX_DELIMITER;
        }
    }
}

namespace {
    use PH7\Framework\Str\Str;

    /**
     * Alias of Str::escape() method.
     *
     * @param array|string $mText
     * @param bool $bStrip
     *
     * @return array|string
     */
    function escape($mText, $bStrip = false)
    {
        return (new Str)->escape($mText, $bStrip);
    }
}
