{include file="inc/header.tpl"}

<h2>{$LANG.welcome|upper} &laquo;{$software_name|upper}&raquo; {$LANG.version|upper} {$software_version}</h2>

{$LANG.CMS_desc}

<p>{$LANG.choose_install_lang}</p>

<select name="l" onchange="document.location.href=this.value" class="center form-control">
    {$lang_select}
</select>

<p class="warning">{$LANG.requirements_desc}</p>
<p class="italic">&bull; {$LANG.requirements2_desc}</p>

<p>
    <button type="button" onclick="document.location.href='{$smarty.const.PH7_URL_SLUG_INSTALL}license'"
        class="btn btn-primary btn-lg">{$LANG.go}</button>
</p>

<!-- Add a real welcome voice! -->
<script src="{$smarty.const.PH7_URL_INSTALL}static/js/artyom.js"></script>
<script>
    artyom.initialize({
        lang: "{$LANG.lang}",
        debug: false,
        speed: 0.9 // Slower the speed voice
    });
</script>
<script>artyom.say("{$LANG.welcome_voice}");</script>

{include file="inc/footer.tpl"}
