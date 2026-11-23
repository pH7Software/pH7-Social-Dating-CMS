<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Form;
use PHPUnit\Framework\TestCase;

final class FormOutputEncodingTest extends TestCase
{
    public function testAjaxConfigurationIsEncodedForJavaScript(): void
    {
        $sAction = '/save");alert(document.domain);//</script>';
        $oForm = new Form('ajax_output_encoding');
        $oForm->configure([
            'action' => $sAction,
            'ajax' => true,
            'method' => 'POST'
        ]);

        $sHtml = $oForm->render(true);
        $sEncodedAction = json_encode(
            $sAction,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR
        );

        $this->assertIsString($sHtml);
        $this->assertStringContainsString('url: ' . $sEncodedAction, $sHtml);
        $this->assertStringContainsString('type: "POST"', $sHtml);
        $this->assertStringNotContainsString('url: "' . $sAction . '"', $sHtml);
    }
}
