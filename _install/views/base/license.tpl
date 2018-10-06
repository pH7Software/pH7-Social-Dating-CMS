{include file="inc/header.tpl"}

<h2>{$LANG.license}</h2>

<p {if !empty($failure)}class="error"{/if}>{$LANG.license_desc}</p>

<p>
    <iframe src="{$smarty.const.PH7_URL_INSTALL}langs/{$current_lang}/license.html">{$LANG.need_frame}</iframe>
</p>

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}license">
    <p>
        <input type="checkbox" name="license_agreed" id="license_agreed" onclick="checkLicenseStatus()"/>
        <label for="license_agreed">{$LANG.agree_license}</label>
    </p>

    <p>
        <input type="checkbox" name="disclaimer" id="disclaimer" onclick="checkLicenseStatus()"/> {$LANG.responsibility_agreement}
    </p>

    <p>
        <button type="submit" name="license_submit" id="next_btn" value="1" disabled="disabled"
            class="btn btn-primary btn-lg">{$LANG.next}</button>
    </p>
</form>

{literal}
    <script>
        function checkLicenseStatus() {
            document.getElementById('next_btn').disabled = (document.getElementById('license_agreed').checked && document.getElementById('disclaimer').checked) ? false : true;
        }
    </script>
{/literal}

{include file="inc/footer.tpl"}
