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

defined('PH7') or exit('Restricted access');

class Curly extends Syntax
{
    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        if (empty($this->sCode)) {
            throw new EmptyCodeException('Parsing code unset!', EmptyCodeException::CURLY_SYNTAX);
        }

        /***** <?php *****/
        $this->sCode = str_replace('{{', '<?php ', $this->sCode);

        /***** ?> *****/
        if (!preg_match('#(;[\s]+}} | ;[\s]+%})#', $this->sCode)) {
            $this->sCode = str_replace(['}}', '%}'], ';?>', $this->sCode);
        } else {
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
        $this->sCode = preg_replace('#{literal}(.+){/literal}#', '$1', $this->sCode);
    }
}
