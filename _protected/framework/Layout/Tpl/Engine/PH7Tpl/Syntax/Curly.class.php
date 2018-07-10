<?php
/***************************************************************************
 * @title            Default Curly syntax for pH7TPl template engine.
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;

defined('PH7') or exit('Restricted access');

class Curly extends Syntax
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

        /***** Includes *****/
        $this->sCode = str_replace(
            '{auto_include}',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $this->registry->action . \'' . PH7Tpl::TEMPLATE_FILE_EXT . '\', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
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
        $this->sCode = str_replace(
            '{def_main_auto_include}',
            '<?php $this->display(\'' . $this->sTplFile . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
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

        /***** <?php *****/
        $this->sCode = str_replace('{{', '<?php ', $this->sCode);

        /***** ?> *****/
        if (!preg_match('#(;(?:\s+)?}}|;(?:\s+)?%})#', $this->sCode)) {
            $this->sCode = str_replace(['}}', '%}'], ';?>', $this->sCode);
        } else {
            // Don't put a semicolon if there is already one
            $this->sCode = str_replace(['}}', '%}'], '?>', $this->sCode);
        }

        /***** <?php echo *****/
        $this->sCode = str_replace('{%', '<?php echo ', $this->sCode);

        /***** if *****/
        $this->sCode = preg_replace('#{if ([^\{\}\n]+)}#', '<?php if($1) { ?>', $this->sCode);

        /***** elseif *****/
        $this->sCode = preg_replace('#{elseif ([^\{\}\n]+)}#', '<?php } elseif($1) { ?>', $this->sCode);

        /***** else *****/
        $this->sCode = str_replace('{else}', '<?php } else { ?>', $this->sCode);

        /***** for *****/
        /*** Example ***/
        /* {for $sData in $aData} <p>Total items: {% $sData_total %} /><br /> Number: {% $sData_i %}<br /> Name: {% $sData %}</p> {/for} */
        $this->sCode = preg_replace(
            '#{for ([^\{\}\n]+) in ([^\{\}\n]+)}#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );

        /***** while *****/
        $this->sCode = preg_replace('#{while ([^\{\}\n]+)}#', '<?php while($1) { ?>', $this->sCode);

        /***** each (foreach) *****/
        $this->sCode = preg_replace(
            '#{each ([^\{\}\n]+) in ([^\{\}\n]+)}#',
            '<?php foreach($2 as $1) { ?>',
            $this->sCode
        );

        /***** endif | endfor | endwhile | endforeach *****/
        $this->sCode = str_replace(['{/if}', '{/for}', '{/while}', '{/each}'], '<?php } ?>', $this->sCode);

        /***** Escape (htmlspecialchars) *****/
        $this->sCode = preg_replace(
            '#{escape ([^\{\}]+)}#',
            '<?php $this->str->escape($1); ?>',
            $this->sCode
        );

        /***** Language *****/
        $this->sCode = preg_replace('#{lang ([^\{\}]+)}#', '<?php echo t($1); ?>', $this->sCode);
        $this->sCode = preg_replace('#{lang}([^\{\}]+){/lang}#', '<?php echo t(\'$1\'); ?>', $this->sCode);

        /***** {literal} JavaScript Code {/literal} *****/
        $this->sCode = preg_replace('#{literal}(.*){/literal}#sU', '$1', $this->sCode);

        /***** Variables *****/
        $this->sCode = preg_replace('#{([a-z0-9_]+)}#i', '<?php echo $$1; ?>', $this->sCode);

        /***** Clears comments {* comment *} *****/
        $this->sCode = preg_replace('#{\*.+\*}#sU', null, $this->sCode);
    }
}
