<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Comment
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Comment;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CommentTableValidationTest extends TestCase
{
    /**
     * The table comes from the URL. An unknown one reached the model, which threw
     * "Invalid data table", so the page failed with a server error instead of a 404.
     */
    #[DataProvider('pageActionsProvider')]
    public function testPageActionChecksTheTableFirst(string $sAction): void
    {
        $this->assertMatchesRegularExpression(
            '/public function ' . $sAction . '\(\)\s*\{\s*\$this->requireValidTable\(\);/',
            $this->readController()
        );
    }

    public function testDeleteChecksThePostedTableBeforeDeleting(): void
    {
        $sPattern = "/post\('table', Type::STRING\);\s*\\\$this->requireValidTable\(\);"
            . "\s*if \(\\\$this->oCommentModel->delete\(/";

        $this->assertMatchesRegularExpression($sPattern, $this->readController());
    }

    public static function pageActionsProvider(): array
    {
        return [
            ['read'],
            ['post'],
            ['add'],
            ['edit']
        ];
    }

    private function readController(): string
    {
        $sContents = file_get_contents(PH7_PATH_SYS_MOD . 'comment/controllers/CommentController.php');
        $this->assertIsString($sContents);

        return $sContents;
    }
}
