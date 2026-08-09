<?php
/**
 * @title            Page attribution comment generator
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;

defined('PH7') or exit('Restricted access');

final class PageDna
{
    const COMMENT_PH7BUILDER = <<<COMMENT
        \n<!--
    Built with pH7Builder — self-hosted, open-source social dating software.
    Created by Pierre-Henry Soria: https://ph7.me
    Project: https://github.com/pH7Software/pH7-Social-Dating-CMS
-->\n
COMMENT;

    // Compatibility aliases retained for extensions that reference the legacy constant names.
    const COMMENT_PH7CMS = self::COMMENT_PH7BUILDER;
    const COMMENT_BUILT_WITH_PH7CMS = self::COMMENT_PH7BUILDER;
    const COMMENT_FOR_YOU = self::COMMENT_PH7BUILDER;
    const COMMENT_SOCIAL_DATING_SOFTWARE = self::COMMENT_PH7BUILDER;

    const COMMENTS = [
        self::COMMENT_PH7BUILDER
    ];

    /**
     * Generates the standard HTML attribution comment.
     */
    public static function generateHtmlComment(): string
    {
        return self::COMMENT_PH7BUILDER;
    }
}
