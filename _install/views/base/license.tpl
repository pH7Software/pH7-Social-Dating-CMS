{include file="inc/header.tpl"}

<h2>{$LANG.license}</h2>

<p>{$LANG.license_desc}</p>

<p>
    <iframe src="{$software_license_url}?l={$current_lang}">{$LANG.need_frame}</iframe>
</p>

<p>
    <input type="checkbox" id="agree" onclick="checkLicenseStatus()"/> <label for="agree">{$LANG.agree_license}</label>
</p>

<p>
    <button type="button" onclick="window.location='{$smarty.const.PH7_URL_SLUG_INSTALL}config_system'"
        id="license-agreed" disabled="disabled" class="btn btn-primary btn-lg">{$LANG.next}</button>
</p>

{literal}
<script>
    function checkLicenseStatus() {
        document.getElementById('license-agreed').disabled = (document.getElementById('agree').checked) ? false : true;
    }
</script>
{/literal}

{include file="inc/footer.tpl"}
