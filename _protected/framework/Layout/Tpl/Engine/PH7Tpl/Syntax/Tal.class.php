<?php
/***************************************************************************
 * @title            Template Attribute Language (TAL) syntax for pH7TPl template engine.
 * @desc             This alternative pH7Tpl syntax is a sort of Template Attribute Language.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;

defined('PH7') or exit('Restricted access');

class Tal extends Syntax implements Parsable
{
    /**
     * Parse XHTML-style syntax.
     *
     * @return void
     */
    public function parse()
    {
        if ($this->isCodeUnset()) {
            throw new EmptyCodeException(
                'Parsing code unset!',
                EmptyCodeException::TAL_SYNTAX
            );
        }

        $this->autoIncludeStatements();
        $this->includeStatements();

        $this->phpOpeningTag();
        $this->phpClosingTag();
        $this->phpOpeningTagWithEchoFunction();
        $this->phpCode();

        $this->ifStatement();
        $this->elseStatement();
        $this->elseifStatement();

        $this->ifSetStatement();
        $this->ifEmptyStatement();
        $this->ifEqualStatement();

        $this->forLoopStatement();
        $this->whileLoopStatement();
        $this->eachLoopStatement();
        $this->closingBlockStructures();

        $this->designModelFunction();

        $this->escapeFunction();

        $this->langFunctions();

        $this->literalFunction();

        $this->variable();

        $this->clearComment();
    }

    public function autoIncludeStatements()
    {
        $this->sCode = preg_replace(
            '#<ph:auto_include ?/?>#',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $this->registry->action . \'' . PH7Tpl::TEMPLATE_FILE_EXT . '\', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#<ph:def_main_auto_include ?/?>#',
            '<?php $this->display(\'' . $this->sTplFile . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
    }

    public function includeStatements()
    {
        $this->sCode = preg_replace(
            '#<ph:include (?:"|\')([^\<\>"\'\n]+)(?:"|\') ?/?>#',
            '<?php $this->display(\'$1\'); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#<ph:main_include (?:"|\')([^\<\>"\'\n]+)(?:"|\') ?/?>#',
            '<?php $this->display(\'$1\', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#<ph:def_main_include (?:"|\')([^\<\>"\'\n]+)(?:"|\') ?/?>#',
            '<?php $this->display(\'$1\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#<ph:manual_include (?:"|\')([^\<\>"\'\n]+)(?:"|\') ?/?>#',
            '<?php $this->display($this->getCurrentController() . PH7_DS . \'$1\', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
    }

    public function phpOpeningTag()
    {
        $this->sCode = str_replace(
            '<ph:code>',
            '<?php ',
            $this->sCode
        );
    }

    public function phpClosingTag()
    {
        if (!preg_match('#;(?:\s+)?</ph:code>$#', $this->sCode)) {
            $this->sCode = str_replace(
                '</ph:code>',
                ';?>',
                $this->sCode
            );
        } else {
            // Don't put a semicolon if there is already one
            $this->sCode = str_replace(
                '</ph:code>',
                '?>',
                $this->sCode
            );
        }
    }

    public function phpOpeningTagWithEchoFunction()
    {
        $this->sCode = preg_replace(
            '#<ph:print value=([^\<\>/\n]+) ?/?>#',
            '<?php echo $1 ?>',
            $this->sCode
        );
    }

    public function phpCode()
    {
        $this->sCode = preg_replace(
            '#<ph:code value=(?:"|\')(.+)(?:"|\') ?/?>#',
            '<?php $1 ?>',
            $this->sCode
        );
    }

    public function ifStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:if test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if($1) { ?>',
            $this->sCode
        );
    }

    public function ifSetStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:if-set test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if(!empty($1)) { ?>',
            $this->sCode
        );
    }

    public function ifEmptyStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:if-empty test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if(empty($1)) { ?>',
            $this->sCode
        );
    }

    public function ifEqualStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:if-equal test=(?:"|\')(\')?([^\<\>,"\'\n]+)(\')?,(\')?([^\<\>,"\'\n]+)(\')?(?:"|\')>#',
            '<?php if($1$2$3 === $4$5$6) { ?>',
            $this->sCode
        );
    }

    public function elseStatement()
    {
        $this->sCode = str_replace(
            '<ph:else>',
            '<?php } else { ?>',
            $this->sCode
        );
    }

    public function elseifStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:else-if test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php } elseif($1) { ?>',
            $this->sCode
        );
    }

    public function forLoopStatement()
    {
        /* <ph:for test="$sData in $aData"> <p>Total items: <ph:print value="$sData_total" /><br /> Number: <ph:print value="$sData_i" /><br /> Name: <ph:print value="$sData" /></p> </ph:for> */
        $this->sCode = preg_replace(
            '#<ph:for test=(?:"|\')([^\<\>"\'\n]+) in ([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );
    }

    public function whileLoopStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:while test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php while($1) { ?>',
            $this->sCode
        );
    }

    public function eachLoopStatement()
    {
        $this->sCode = preg_replace(
            '#<ph:each test=(?:"|\')([^\<\>"\'\n]+) in ([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php foreach($2 as $1) { ?>',
            $this->sCode
        );
    }

    /**
     * endif | endfor | endwhile | endforeach
     *
     * @return void
     */
    public function closingBlockStructures()
    {
        $this->sCode = str_replace(
            ['</ph:if>', '</ph:else>', '</ph:else-if>', '</ph:for>', '</ph:while>', '</ph:each>', '</ph:if-set>', '</ph:if-empty>', '</ph:if-equal>'],
            '<?php } ?>',
            $this->sCode
        );
    }

    /**
     * [[VARIABLE_NAME]]
     *
     * @return void
     */
    public function variable()
    {
        $this->sCode = preg_replace(
            '#\[\[([a-z0-9_]+)\]\]#i',
            '<?php echo $$1; ?>',
            $this->sCode
        );
    }

    /**
     * <ph:designmodel value="[a-z0-9_]+()">
     *
     * @return void
     */
    public function designModelFunction()
    {
        $this->sCode = preg_replace(
            '#<ph:designmodel value=(?:"|\')([a-z0-9_]+)\((.*)\)(?:"|\') ?/?>#i',
            '<?php $this->designModel->$1($2) ?>',
            $this->sCode
        );
    }

    /**
     * Escape (htmlspecialchars).
     *
     * @return void
     */
    public function escapeFunction()
    {
        $this->sCode = preg_replace(
            '#<ph:escape value=([^\<\>/\n]+) ?/?>#',
            '<?php this->str->escape($1); ?>',
            $this->sCode
        );
    }

    /**
     * Translation (with gettext).
     *
     * @return void
     */
    public function langFunctions()
    {
        $this->sCode = preg_replace(
            '#<ph:lang value=([^\<\>/\n]+) ?/?>#',
            '<?php echo t($1); ?>',
            $this->sCode
        );

        $this->sCode = preg_replace(
            '#<ph:lang>([^\<\>/\n]+)</ph:lang>#',
            '<?php echo t(\'$1\'); ?>',
            $this->sCode
        );
    }

    /**
     * Allow data blocks to be taken literally.
     * <ph:literal></ph:literal> block is typically used around JS or CSS code.
     *
     * @return void
     */
    public function literalFunction()
    {
        $this->sCode = preg_replace(
            '#<ph:literal>(.+)</ph:literal>#sU',
            '$1',
            $this->sCode
        );
    }

    /**
     * Clears comments: ### comment here ###
     */
    public function clearComment()
    {
        $this->sCode = preg_replace(
            '/###.+###/sU',
            null,
            $this->sCode
        );
    }
}
