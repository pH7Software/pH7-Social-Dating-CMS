{include file="inc/header.tpl"}

<h2>{$LANG.welcome|upper} &laquo;{$software_name|upper}&raquo; {$LANG.version|upper} {$software_version}</h2>

{$LANG.CMS_desc}
<p>&nbsp;</p>

<p>{$LANG.choose_install_lang}</p>

<select name="l" onchange="document.location.href=this.value">
    {$lang_select}
</select>

<p class="warning">{$LANG.requirements_desc}</p>

<p><a href="{$smarty.const.PH7_URL_SLUG_INSTALL}license">{$LANG.go}</a></p>

<!-- Add a welcome sound -->
<audio style="display:none" autoplay="autoplay" src="{$smarty.const.PH7_URL_ROOT}static/sound/welcome.mp3"></audio>

{include file="inc/footer.tpl"}
