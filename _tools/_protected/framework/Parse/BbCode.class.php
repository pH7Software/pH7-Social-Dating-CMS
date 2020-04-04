<?php
/**
 * @title            BBCode Class
 * @desc             BBCode Markup Parser with HTML5 support.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          0.8
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

class BbCode extends Code
{
    /**
     * @param string $sText
     */
    public function __construct($sText)
    {
        $this->sText = $sText;

        parent::__construct();
    }

    /**
     * @return string The parsed code
     */
    public function __toString()
    {
        return $this->sText;
    }

    /**
     * Run the parse methods
     *
     * @return void
     */
    protected function run()
    {
        $this->paragraph();
        $this->parse();
    }

    /**
     * Parse text and processing
     *
     * @return void
     */
    protected function parse()
    {
        // [h]eading
        $this->sText = preg_replace(
            '/\[(h\d{1,6})](.+?)\[\/(h\d{1,6})]/i',
            '<\1>\2</\3>',
            $this->sText
        );

        // [s]trong
        $this->sText = preg_replace(
            '/\[b](.+?)\[\/b]/i',
            '<strong>\1</strong>',
            $this->sText
        );

        // [i]talic
        $this->sText = preg_replace(
            '/\[i](.+?)\[\/i]/i',
            '<em>\1</em>',
            $this->sText
        );

        // [u]nderline
        $this->sText = preg_replace(
            '/\[u](.+?)\[\/u]/i',
            '<span style="text-decoration:underline">\1</span>',
            $this->sText
        );

        // [del]ete
        $this->sText = preg_replace(
            '/\[del](.+?)\[\/del]/i',
            '<span style="text-decoration:line-through">\1</span>',
            $this->sText
        );

        // [q]uote
        $this->sText = preg_replace(
            '/\[q](.+?)\[\/q]/i',
            '<q>\1</q>',
            $this->sText
        );

        // Blockquote
        $this->sText = preg_replace(
            '/\[blockquote](.+?)\[\/blockquote]/i',
            '<blockquote><p>\1</p></blockquote>',
            $this->sText
        );

        // Code
        $this->sText = preg_replace(
            '/\[code](.+?)\[\/code]/is',
            '<code>\1</code>',
            $this->sText
        );

        // Size (in pixels)
        $this->sText = preg_replace(
            '/\[size=(\d{1,2})](.+?)\[\/size]/i',
            '<span style="font-size:\1px">\2</span>',
            $this->sText
        );

        // Center
        $this->sText = preg_replace(
            '/\[center](.+?)\[\/center]/i',
            '<div style="text-align:center">\1</div>',
            $this->sText
        );

        // Color
        $this->sText = preg_replace(
            '/\[color=(green|lime|olive|red|maroon|navy|blue|teal|aqua|yellow|purple|fuchsia|gold|black|silver|gray|white)\](.+?)\[\/color\]/is',
            '<span style="color:\1">\2</span>',
            $this->sText
        );

        // Line breaks
        $this->sText = str_replace("\n", '<br />', $this->sText);

        /** List **/
        // Unordered List:
        $this->sText = preg_replace(
            '/\[ul](.+?)\[\/ul]/i',
            '<ul>\1</ul>',
            $this->sText
        );
        // List Item
        $this->sText = preg_replace(
            '/\[li](.+?)\[\/li]/i',
            '<li>\1</li>',
            $this->sText
        );
        // Ordered List
        $this->sText = preg_replace(
            '/\[ol](.+?)\[\/ol]/i',
            '<ol>\1</ol>',
            $this->sText
        );

        // [url]link[/url]
        $this->sText = preg_replace(
            '/\[url]([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\[\/url]/i',
            '<a href="\1">\1</a>',
            $this->sText
        );

        // [url=url]lien[/url]
        $this->sText = preg_replace(
            '/\[url=([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)](.+?)\[\/url]/i',
            '<a href="\1" title="\2">\2</a>',
            $this->sText
        );

        // [img]img link[/img]
        $this->sText = preg_replace(
            '/\[img]([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\[\/img\]/si',
            '<img src="\1" alt="Image" />',
            $this->sText
        );

        // [img=img link]title[/img]
        $this->sText = preg_replace(
            '/\[img=([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)](.+?)\[\/img\]/si',
            '<img src="\1" alt="\2" title="\2" />',
            $this->sText
        );

        // [email]email address[/email]
        $this->sText = preg_replace(
            '/\[email]([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)\[\/email]/i',
            '<a href="mailto:\1">\1</a>',
            $this->sText
        );

        // [email=email address]email text[/email]
        $this->sText = preg_replace(
            '/\[email=([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)](.+?)\[\/email]/i',
            '<a href="mailto:\1" title="\2">\2</a>',
            $this->sText
        );
    }
}
