<?php
/**
 * @title            Mail Layout Class
 * @desc             Handler for the template emails.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Registry\Registry;

class Mail
{
    /** @var bool */
    private $bSubFooter = false;

    /**
     * Header tags HTML.
     *
     * @return string HTML Contents.
     */
    public function header()
    {
        return '<!DOCTYPE html><html>
            <head><meta charset="utf-8" />
            <title>' . Registry::getInstance()->site_name . '</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <style>
              * {margin:0; padding:0;}
              body {font-family:Helvetica Neue, Helvetica, Arial, Verdana, sans-serif; background-color:#eee;}
              #container {padding:20px; background-color:#eee; text-align:center;}
              #sub_container {color:#333; padding:20px; border:1px solid #999; background-color:#fff; font-size:14px; border-radius: 5px;}
              .logo {border-bottom:2px solid #eee; padding-bottom:10px; margin-bottom:20px; font-weight:bold; font-size:22px; color:#999;}
              .break {padding:10px; margin:10px 20px; border-top:1px solid #C8C8C8;}
              a{color:#08c; outline-style:none; cursor:pointer;}
              a:link, a:visited{text-decoration:none;}
              a:hover, a:active{color:#F24C9E; text-decoration:underline; text-shadow:0 0 5px #fff,0 0 8px rgba(255,255,255,.6);}
              table {width:600px; margin:auto; border:0; border-collapse:separate; border-spacing:0px;}
              td {vertical-align:top;}
              .foot1 {font-size:11px; margin-top:5px; color:#666;}
              .foot2 {font-size:10px; margin-top:5px; font-style:italic; color:#999;}
            </style>
            </head>
            <body>
            <div id="container">';
    }

    /**
     * Sub Header HTML with the site logo.
     *
     * @return string HTML Contents.
     */
    public function subHeader()
    {
        return '<table>
            <tr><td>
            <div id="sub_container">
            <h1 class="logo"><a href="' . Registry::getInstance()->site_url . '">' . Registry::getInstance()->site_name . '</a></h1>';
    }

    /**
     * Footer HTML.
     *
     * @return string HTML Contents.
     */
    public function bottomFooter()
    {
        return t('Best regards,<br />%0% team.', '<a href="%site_url%">%site_name%</a>');
    }

    /**
     * @param string $sEmail The email address from a user to indicate in the privacy policy against spam.
     *
     * @return string HTML Contents.
     */
    public function privacyPolicyFooter($sEmail)
    {
        return t('This message was sent to %0%.<br /> You receive this message because you are registered on %1%.<br /> You can change the email alerts by visiting your account.', $sEmail, '<a href="%site_url%">%site_name%</a>');
    }

    /**
     * @param string $sEmail The email address from a user to indicate in the privacy policy against spam.
     *
     * @return string HTML Contents.
     */
    public function subFooter($sEmail)
    {
        $this->bSubFooter = true;

        return '</div>
            </td></tr>
            <tr><td>
            <div class="break"></div>
            <p class="foot1">' . $this->bottomFooter() . '</p>
            <p class="foot2">' . $this->privacyPolicyFooter($sEmail) . '</p>
            <p class="foot2">' . $this->link() . '</p>
            </td></tr>
            </table>';
    }

    /**
     * End tags HTML.
     *
     * @return string HTML Contents.
     */
    public function footer()
    {
        $sHtmlFooter = !$this->bSubFooter ? '</div></td></tr></table>' : '';

        return $sHtmlFooter . '</div></body></html>';
    }

    /**
     * Provide a "Powered By" link for the email footer.
     *
     * @return string
     */
    final protected function link()
    {
        ob_start();
        (new Design)->link(
            true,
            true,
            false,
            false,
            true
        );

        $sOutputLink = ob_get_contents();
        ob_end_clean();

        return $sOutputLink;
    }
}
