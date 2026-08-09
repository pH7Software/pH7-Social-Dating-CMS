<?php

/**
 * @desc             Provides authenticated manual upgrade guidance.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel;
use PH7\Framework\Security\Version;

final class UpgradeCoreFile
{
    private string $sHtml;

    public function __construct()
    {
        if (!AdminCore::auth()) {
            http_response_code(403);
            $this->sHtml = '<h2 class="error">' .
                t('You must be logged in as administrator to upgrade your site.') .
                '</h2>';

            return;
        }

        $this->sHtml = $this->getManualUpgradeNotice();
    }

    public function display(): void
    {
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>',
        t('Upgrade %software_name%'),
        '</title><meta name="viewport" content="width=device-width,initial-scale=1">',
        '<meta name="robots" content="noindex"><style>',
        'body{background:#EFEFEF;color:#555;font:normal 11pt Arial,Helvetica,sans-serif;margin:0;padding:2rem}',
        '.center{margin:0 auto;max-width:760px}.notice{background:#fff;border:1px solid #ddd;border-radius:6px;padding:1.5rem}',
        '.error{color:#c00}li{margin:.75rem 0}a{color:#0783c2}',
        '</style></head><body><main class="center"><section class="notice">',
        $this->sHtml,
        '</section></main></body></html>';
    }

    private function getManualUpgradeNotice(): string
    {
        $sReleaseUrl = Kernel::SOFTWARE_GIT_REPO_URL . '/releases';

        return '<h1>' . t('Manual upgrade required') . '</h1>' .
            '<p>' . t('For your safety, automatic upgrades are currently unavailable.') . '</p>' .
            '<p><strong>' . t('Back up your website files and database before continuing.') . '</strong></p>' .
            '<ol>' .
            '<li><a href="' . $sReleaseUrl . '" target="_blank" rel="noopener noreferrer">' .
            t('Download the published release from the official GitHub releases page.') . '</a></li>' .
            '<li><a href="' . Version::UPGRADE_DOC_URL . '" target="_blank" rel="noopener noreferrer">' .
            t('Follow the manual upgrade guide carefully.') . '</a></li>' .
            '<li>' . t('Apply the migration matching your installed version, then verify the site before reopening it.') . '</li>' .
            '</ol>';
    }
}

(new UpgradeCoreFile())->display();
