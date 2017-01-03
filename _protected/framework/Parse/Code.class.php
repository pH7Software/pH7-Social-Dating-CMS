<?php
/**
 * @title            Code Abstract Class
 * @desc             The Prototype for the extends code classes.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.0
 */

namespace PH7\Framework\Parse;
defined('PH7') or exit('Restricted access');

abstract class Code
{

    /**
     * @access protected
     * @var string $sText
     */
    protected $sText;

    /**
     * @access public
     */
    public function __construct()
    {
        $this->sanitize();
        $this->convert();
        $this->run();
    }

    /**
     * @access protected
     * @return void
     */
    protected function sanitize()
    {
        $this->sText = preg_replace('/<script(.*?)>(.*?)<\/script>/is', '', $this->sText);
        $this->convert();
    }

    /**
     * Paragraph
     *
     * @access protected
     * @return void
     */
    protected function paragraph()
    {
        $this->sText = '<p>' . str_replace("\n\n", '</p><p>', $this->sText) . '</p>';
    }

    /**
     * Convert the space
     *
     * @access protected
     * @return void
     */
    protected function convert()
    {
        // Convert Windows (\r\n) to Unix (\n)
        $this->sText = str_replace("\r\n", "\n", $this->sText);

        // Convert Macintosh (\r) to Unix (\n)
        $this->sText = str_replace("\r", "\n", $this->sText);
    }

    /**
     * Displaying the text
     *
     * @access public
     * @abstract
     * @return string The code parsed
     */
    abstract public function __toString();

    /**
     * Run the parse methods
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function run();
}
