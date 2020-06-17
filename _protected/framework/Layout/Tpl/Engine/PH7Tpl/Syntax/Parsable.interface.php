<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

interface Parsable
{
    public function autoIncludeStatements();

    public function includeStatements();

    public function phpOpeningTag();

    public function phpClosingTag();

    public function phpOpeningTagWithEchoFunction();

    public function ifStatement();

    public function elseifStatement();

    public function elseStatement();

    public function forLoopStatement();

    public function whileLoopStatement();

    public function eachLoopStatement();

    public function closingBlockStructures();

    public function variable();

    public function designModelFunction();

    public function escapeFunction();

    public function langFunctions();

    public function literalFunction();

    public function clearComment();
}
