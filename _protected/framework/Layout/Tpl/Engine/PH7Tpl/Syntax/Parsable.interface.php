<?php
/***************************************************************************
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

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
