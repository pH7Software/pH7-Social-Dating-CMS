<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PH7\Framework\Str\Str;

class ShareEmbedCoreForm
{
    /**
     * Embed code
     *
     * @param string $sFileUrl
     * @param int $iEmbedWidth Width of the embed code.
     * @param int $iEmbedHeight Height of the embed code.
     * @param int $iWidth Width of the form in pixel.
     *
     * @return void
     */
    public static function display($sFileUrl, $iEmbedWidth = 580, $iEmbedHeight = 450, $iWidth = null)
    {
        $sEscapedFileUrl = (new Str())->escapeAttribute($sFileUrl);
        $iEmbedWidth = max(1, (int)$iEmbedWidth);
        $iEmbedHeight = max(1, (int)$iEmbedHeight);
        $sEmbedCode = sprintf(
            '<video src="%s" width="%d" height="%d" controls preload="metadata"></video>',
            $sEscapedFileUrl,
            $iEmbedWidth,
            $iEmbedHeight
        );

        $oForm = new \PFBC\Form('form_share_embed', $iWidth);
        $oForm->configure(['action' => '', 'class' => 'center']);
        $oForm->addElement(
            new Textarea(
                t('Embed Code'),
                'embed',
                [
                    'value' => $sEmbedCode,
                    'readonly' => 'readonly',
                    'onclick' => 'this.select()'
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<br />'));
        $oForm->render();
    }
}
