{include file="inc/header.tpl"}

<h2>{$LANG.welcome|upper} &laquo;{$software_name|upper}&raquo; {$LANG.version|upper} {$software_version}</h2>

{if !empty($install_access_required)}
    <p>{$LANG.install_access_intro}</p>

    {if empty($install_access_configured)}
        <p class="error">{$LANG.install_access_not_configured}</p>
        <p><code>php _install/create-install-token.php</code></p>
    {else}
        {if !empty($install_access_error)}
            <p class="error">{$install_access_error|escape}</p>
        {/if}

        <form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}index">
            <input type="hidden" name="action_token" value="{$action_token|escape}" />
            <p>
                <label for="install_access_token">{$LANG.install_access_token}:</label><br />
                <input
                    type="password"
                    name="install_access_token"
                    id="install_access_token"
                    minlength="32"
                    autocomplete="off"
                    required="required"
                />
            </p>
            <p>
                <button type="submit" name="install_access_submit" value="1" class="btn btn-primary btn-lg">
                    {$LANG.next}
                </button>
            </p>
        </form>
    {/if}
{else}
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
{/if}

{include file="inc/footer.tpl"}
