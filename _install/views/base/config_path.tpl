{include file="inc/header.tpl"}

<h2>{$LANG.config_path}</h2>

{include file="inc/errors.tpl"}

{if !empty($stale_constants_recovery)}
    <p class="warning">{$stale_constants_recovery|escape}</p>
{/if}

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}config_path">
    <input type="hidden" name="action_token" value="{$action_token|escape}" />
    <fieldset>
        <p>
            <span class="mandatory">*</span> <label for="path_protected">{$LANG.path_protected}:</label><br />
            <span class="small">{$LANG.desc_config_path}</span><br />
            <input type="text" name="path_protected" id="path_protected" value="{$smarty.session.val.path_protected|escape}" required="required" />
        </p>

        <p>
            <button type="submit" class="btn btn-primary btn-lg">{$LANG.next}</button>
        </p>
    </fieldset>
</form>

{include file="inc/footer.tpl"}
