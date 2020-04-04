<?php
/**
 * @title            Markdown Markup Parser Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.0
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

class Markdown extends Code
{
    /**
     * @param string The text formatted in Markdown
     */
    public function __construct($sText)
    {
        $this->sText = $sText;

        parent::__construct();
    }

    /**
     * @return string The code parsed
     */
    public function __toString()
    {
        return $this->sText;
    }

    /**
     * Run the transform methods
     *
     * @return void
     */
    protected function run()
    {
        $this->strong();
        $this->italic();
        $this->code();
        $this->img();
        $this->link();
        $this->blockquote();
        $this->headings();
        $this->alternativeHeadings();
        $this->paragraph();
        $this->br();
        $this->hr();
    }

    /**
     * Strong
     *
     * @return void
     */
    protected function strong()
    {
        // Strong emphasis
        $this->sText = preg_replace(
            '/__(.+?)__/s',
            '<strong>\1</strong>',
            $this->sText
        );

        // Alternative syntax
        $this->sText = preg_replace(
            '/\*\*(.+?)\*\*/s',
            '<strong>\1</strong>',
            $this->sText
        );
    }

    /**
     * Italic
     *
     * @return void
     */
    protected function italic()
    {
        // Emphasis
        $this->sText = preg_replace(
            '/_([^_]+)_/',
            '<em>\1</em>',
            $this->sText
        );

        // Alternative syntax
        $this->sText = preg_replace(
            '/\*([^\*]+)\*/',
            '<em>\1</em>',
            $this->sText
        );
    }

    /**
     * HTML code tag
     *
     * @return void
     */
    protected function code()
    {
        $this->sText = preg_replace(
            '/`(.+?)`/s',
            '<code>\1</code>',
            $this->sText
        );
    }

    /**
     * Hyperlink tag
     *
     * @return void
     */
    protected function link()
    {
        // [linked text](link URL)
        $this->sText = preg_replace(
            '/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i',
            '<a href="\2">\1</a>',
            $this->sText
        );

        // [linked text][link URL] (alternative syntax)
        $this->sText = preg_replace(
            '/\[([^\]]+)]\[([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\]/i',
            '<a href="\2">\1</a>',
            $this->sText
        );

        // [linked text]: link URL "title" (alternative syntax)
        $this->sText = preg_replace(
            '/\[([^\]]+)]: ([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+) "([^"]+)"/i',
            '<a href="\2" title="\3">\1</a>',
            $this->sText
        );
    }

    /**
     *
     * Images
     *
     * @return void
     */
    protected function img()
    {
        // With title ![alt image](url image) "title of image"
        $this->sText = preg_replace(
            '/!\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\) "([^"]+)"/',
            '<img src="\2" alt="\1" title="\3" />',
            $this->sText
        );

        // Without title ![alt image](url image)
        $this->sText = preg_replace(
            '/!\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/',
            '<img src="\2" alt="\1" />',
            $this->sText
        );
    }

    /**
     * Blockquote
     *
     * @return void
     */
    protected function blockquote()
    {
        // Blockquotes
        $this->sText = preg_replace(
            '/> "(.+?)"/',
            '<blockquotes><p>\1</p></blockquote>',
            $this->sText
        );
    }

    /**
     * Break line
     *
     * @return void
     */
    protected function br()
    {
        // Line breaks
        $this->sText = str_replace("\n", '<br />', $this->sText);
    }

    /**
     * Thematic break
     *
     * @return void
     */
    protected function hr()
    {
        $this->sText = preg_replace(
            '/^(\s)*----+(\s*)$/m',
            '<hr />',
            $this->sText
        );
    }

    /**
     * Headings
     *
     * @return void
     */
    protected function headings()
    {
        // h1
        $this->sText = preg_replace(
            '/# (.+?)\n/',
            '<h1>\1</h1>',
            $this->sText
        );

        // h2
        $this->sText = preg_replace(
            '/## (.+?)\n/',
            '<h2>\1</h2>',
            $this->sText
        );

        // h3
        $this->sText = preg_replace(
            '/### (.+?)\n/',
            '<h3>\1</h3>',
            $this->sText
        );

        // h4
        $this->sText = preg_replace(
            '/#### (.+?)\n/',
            '<h4>\1</h4>',
            $this->sText
        );

        // h5
        $this->sText = preg_replace(
            '/##### (.+?)\n/',
            '<h5>\1</h5>',
            $this->sTex
        );
    }

    /**
     * Alternative heading syntaxes
     *
     * @return void
     */
    private function alternativeHeadings()
    {
        // h1
        $this->sText = preg_replace(
            '/=======(.+?)=======/s',
            '<h1>\1</h1>',
            $this->sText
        );

        // h2
        $this->sText = preg_replace(
            '/======(.+?)======/s',
            '<h2>\1</h2>',
            $this->sText
        );

        // h3
        $this->sText = preg_replace(
            '/=====(.+?)=====/s',
            '<h3>\1</h3>',
            $this->sText
        );

        // h4
        $this->sText = preg_replace(
            '/====(.+?)====/s',
            '<h4>\1</h4>',
            $this->sText
        );

        // h5
        $this->sText = preg_replace(
            '/===(.+?)===/s',
            '<h5>\1</h5>',
            $this->sText
        );
    }
}

