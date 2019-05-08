<?php
/***************************************************************************
 * @title            Default Curly syntax for pH7TPl template engine.
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;

defined('PH7') or exit('Restricted access');

class Curly extends Syntax implements Parsable
{
    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        if ($this->isCodeUnset()) {
            throw new EmptyCodeException(
                'Parsing code unset!',
                EmptyCodeException::CURLY_SYNTAX
            );
        }

        $this->autoIncludeStatements();
        $this->includeStatements();

        $this->phpOpeningTag();
        $this->phpClosingTag();
        $this->phpOpeningTagWithEchoFunction();

        $this->ifStatement();
        $this->elseStatement();
        $this->elseifStatement();

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
        $this->sCode = str_replace(
            '{auto_include}',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $this->registry->action . \'' . PH7Tpl::TEMPLATE_FILE_EXT . '\', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = str_replace(
            '{def_main_auto_include}',
            '<?php $this->display(\'' . $this->sTplFile . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
    }

    public function includeStatements()
    {
        $this->sCode = preg_replace(
            '#{include ([^\{\}\n]+)}#',
            '<?php $this->display($1); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{main_include ([^\{\}\n]+)}#',
            '<?php $this->display($1, PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{def_main_include ([^\{\}\n]+)}#',
            '<?php $this->display($1, PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{manual_include ([^\{\}\n]+)}#',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $1, $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
    }

    public function phpOpeningTag()
    {
        $this->sCode = str_replace('{{', '<?php ', $this->sCode);
    }

    public function phpClosingTag()
    {
        if (!preg_match('#(;(?:\s+)?}}|;(?:\s+)?%})#', $this->sCode)) {
            $this->sCode = str_replace(
                ['}}', '%}'],
                ';?>',
                $this->sCode
            );
        } else {
            // Don't put a semicolon if there is already one
            $this->sCode = str_replace(
                ['}}', '%}'],
                '?>',
                $this->sCode
            );
        }
    }

    public function phpOpeningTagWithEchoFunction()
    {
        $this->sCode = str_replace(
            '{%',
            '<?php echo ',
            $this->sCode
        );
    }

    public function ifStatement()
    {
        $this->sCode = preg_replace(
            '#{if ([^\{\}\n]+)}#',
            '<?php if($1) { ?>',
            $this->sCode
        );
    }

    public function elseStatement()
    {
        $this->sCode = str_replace(
            '{else}',
            '<?php } else { ?>',
            $this->sCode
        );
    }

    public function elseifStatement()
    {
        $this->sCode = preg_replace(
            '#{elseif ([^\{\}\n]+)}#',
            '<?php } elseif($1) { ?>',
            $this->sCode
        );
    }

    public function forLoopStatement()
    {
        /* {for $sData in $aData} <p>Total items: {% $sData_total %} /><br /> Number: {% $sData_i %}<br /> Name: {% $sData %}</p> {/for} */
        $this->sCode = preg_replace(
            '#{for ([^\{\}\n]+) in ([^\{\}\n]+)}#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );
    }

    public function whileLoopStatement()
    {
        $this->sCode = preg_replace(
            '#{while ([^\{\}\n]+)}#',
            '<?php while($1) { ?>',
            $this->sCode
        );
    }

    public function eachLoopStatement()
    {
        $this->sCode = preg_replace(
            '#{each ([^\{\}\n]+) in ([^\{\}\n]+)}#',
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
            ['{/if}', '{/for}', '{/while}', '{/each}'],
            '<?php } ?>',
            $this->sCode
        );
    }

    /**
     * {VARIABLE_NAME}
     *
     * @return void
     */
    public function variable()
    {
        $this->sCode = preg_replace(
            '#{([a-z0-9_]+)}#i',
            '<?php echo $$1; ?>',
            $this->sCode
        );
    }

    /**
     * {designModel.[a-z0-9_]+()}
     *
     * @return void
     */
    public function designModelFunction()
    {
        $this->sCode = preg_replace(
            '#{designModel\.([a-z0-9_]+)\((.*)\)}#i',
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
            '#{escape ([^\{\}]+)}#',
            '<?php $this->str->escape($1); ?>',
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
            '#{lang ([^\{\}]+)}#',
            '<?php echo t($1); ?>',
            $this->sCode
        );

        $this->sCode = preg_replace(
            '#{lang}([^\{\}]+){/lang}#',
            '<?php echo t(\'$1\'); ?>',
            $this->sCode
        );
    }

    /**
     * {literal} JavaScript Code {/literal}
     *
     * @return void
     */
    public function literalFunction()
    {
        $this->sCode = preg_replace(
            '#{literal}(.*){/literal}#sU',
            '$1',
            $this->sCode
        );
    }

    /**
     * Clears comments {* comment *}
     *
     * @return void
     */
    public function clearComment()
    {
        $this->sCode = preg_replace(
            '#{\*.+\*}#sU',
            null,
            $this->sCode
        );
    }
}
