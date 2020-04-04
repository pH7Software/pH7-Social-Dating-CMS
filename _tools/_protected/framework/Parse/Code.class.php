<?php
/**
 * @title            Code Abstract Class
 * @desc             The Prototype for the extends code classes.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.0
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

abstract class Code
{
    const REGEX_SCRIPT_BLOCK_PATTERN = '/<script(.*?)>(.*?)<\/script>/is';

    /** @var string */
    protected $sText;

    public function __construct()
    {
        $this->sanitize();
        $this->convertSpaces();
        $this->run();
    }

    /**
     * @return void
     */
    protected function sanitize()
    {
        $this->sText = preg_replace(static::REGEX_SCRIPT_BLOCK_PATTERN, '', $this->sText);
        $this->convertSpaces();
    }

    /**
     * Paragraph
     *
     * @return void
     */
    protected function paragraph()
    {
        $this->sText = '<p>' . str_replace("\n\n", '</p><p>', $this->sText) . '</p>';
    }

    /**
     * Convert the space
     *
     * @return void
     */
    protected function convertSpaces()
    {
        // Convert Windows (\r\n) to Unix (\n)
        $this->sText = str_replace("\r\n", "\n", $this->sText);

        // Convert Macintosh (\r) to Unix (\n)
        $this->sText = str_replace("\r", "\n", $this->sText);
    }

    /**
     * Displaying the text
     *
     * @return string The code parsed
     */
    abstract public function __toString();

    /**
     * Run the parse methods
     *
     * @return void
     */
    abstract protected function run();
}
