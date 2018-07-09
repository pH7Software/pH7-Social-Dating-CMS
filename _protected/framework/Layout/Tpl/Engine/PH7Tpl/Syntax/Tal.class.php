<?php
/***************************************************************************
 * @title            Template Attribute Language (TAL) syntax for pH7TPl template engine.
 * @desc             This alternative pH7Tpl syntax is a sort of Template Attribute Language.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

defined('PH7') or exit('Restricted access');

class Tal extends Syntax
{
    /**
     * Parse XHTML-style syntax.
     *
     * @return void
     */
    public function parse()
    {
        if (empty($this->sCode)) {
            throw new EmptyCodeException(
                'Parsing code unset!',
                EmptyCodeException::TAL_SYNTAX
            );
        }

        /***** <?php *****/
        $this->sCode = str_replace('<ph:code>', '<?php ', $this->sCode);

        /***** ?> *****/
        if (!preg_match('#;(?:\s+)?</ph:code>$#', $this->sCode)) {
            $this->sCode = str_replace('</ph:code>', ';?>', $this->sCode);
        } else {
            // Don't put a semicolon if there is already one
            $this->sCode = str_replace('</ph:code>', '?>', $this->sCode);
        }

        /***** <?php ?> *****/
        $this->sCode = preg_replace(
            '#<ph:code value=(?:"|\')(.+)(?:"|\') ?/?>#',
            '<?php $1 ?>',
            $this->sCode
        );

        /***** <?php echo ?> *****/
        $this->sCode = preg_replace(
            '#<ph:print value=([^\<\>/\n]+) ?/?>#',
            '<?php echo $1 ?>',
            $this->sCode
        );

        /***** if *****/
        $this->sCode = preg_replace(
            '#<ph:if test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if($1) { ?>',
            $this->sCode
        );

        /***** if isset *****/
        $this->sCode = preg_replace(
            '#<ph:if-set test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if(!empty($1)) { ?>',
            $this->sCode
        );

        /***** if empty *****/
        $this->sCode = preg_replace(
            '#<ph:if-empty test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php if(empty($1)) { ?>',
            $this->sCode
        );

        /***** if equal *****/
        $this->sCode = preg_replace(
            '#<ph:if-equal test=(?:"|\')([^\<\>,"\'\n]+)(?:"|\'),(?:"|\')([^\<\>,"\'\n]+)(?:"|\')>#',
            '<?php if($1 == $2) { ?>',
            $this->sCode
        );

        /***** elseif *****/
        $this->sCode = preg_replace(
            '#<ph:else-if test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php elseif($1) { ?>',
            $this->sCode
        );

        /***** else *****/
        $this->sCode = str_replace('<ph:else>', '<?php else { ?>', $this->sCode);

        /***** for *****/
        /*** Example ***/
        /* <ph:for test="$sData in $aData"> <p>Total items: <ph:print value="$sData_total" /><br /> Number: <ph:print value="$sData_i" /><br /> Name: <ph:print value="$sData" /></p> </ph:for> */
        $this->sCode = preg_replace(
            '#<ph:for test=(?:"|\')([^\<\>"\'\n]+) in ([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );

        /***** while *****/
        $this->sCode = preg_replace(
            '#<ph:while test=(?:"|\')([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php while($1) { ?>',
            $this->sCode
        );

        /***** each (foreach) *****/
        $this->sCode = preg_replace(
            '#<ph:each test=(?:"|\')([^\<\>"\'\n]+) in ([^\<\>"\'\n]+)(?:"|\')>#',
            '<?php foreach($2 as $1) { ?>',
            $this->sCode
        );

        /***** endif | endfor | endwhile | endforeach *****/
        $this->sCode = str_replace(
            ['</ph:if>', '</ph:else>', '</ph:else-if>', '</ph:for>', '</ph:while>', '</ph:each>', '</ph:if-set>', '</ph:if-empty>', '</ph:if-equal>'],
            '<?php } ?>',
            $this->sCode
        );

        /***** Escape (htmlspecialchars) *****/
        $this->sCode = preg_replace(
            '#<ph:escape value=([^\<\>/\n]+) ?/?>#',
            '<?php this->str->escape($1); ?>',
            $this->sCode
        );

        /***** Translate (Gettext) *****/
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

        /***** literal JavaScript Code *****/
        $this->sCode = preg_replace('#<ph:literal>(.+)</ph:literal>#sU', '$1', $this->sCode);
    }
}
