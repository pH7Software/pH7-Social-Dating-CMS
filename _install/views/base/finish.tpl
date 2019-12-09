{include file="inc/header.tpl"}

<h2>{$LANG.finish}</h2>

<p class="bold">
    <a href="{$smarty.const.PH7_URL_ROOT}" target="_blank">{$LANG.go_your_site}</a> &nbsp; | &nbsp; <a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank">{$LANG.go_your_admin_panel}</a> (<a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank"><em class="text-info">{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}</em></a>)
</p>

{if !empty($admin_login_email) && !empty($admin_username)}
    <ul>
        <li>{$LANG.admin_url}: <a class="underline" href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank"><strong class="text-info">{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}</strong></a></li>
        <li>{$LANG.admin_login_email}: <strong class="text-info">{$admin_login_email|escape}</strong></li>
        <li>{$LANG.admin_username}: <strong class="text-info">{$admin_username|escape}</strong></li>
    </ul>
{/if}

<p>
    <span class="bold">{$LANG.will_you_make_donation}</span> <a class="underline" href="{$patreon_url}" target="_blank" rel="noopener noreferrer">{$LANG.donate_here}</a><br />
    <small>
        <a href="{$paypal_donate_url}" target="_blank" rel="noopener noreferrer">{$LANG.or_paypal_donation}</a>️
    </small>
</p>

<hr />

<form action="{$smarty.const.PH7_URL_SLUG_INSTALL}finish" method="post">
    <p>
        <button class="button" type="submit" name="confirm_remove_install" value="1" onclick="return confirm('{$LANG.confirm_remove_install_folder_auto}')">{$LANG.remove_install_folder_auto}</button><br /><br />
        <span class="italic">{$LANG.remove_install_folder}</span>
    </p>
</form>

<!-- Add a "completion" sound -->
<audio style="display:none" autoplay="autoplay" src="{$smarty.const.PH7_URL_ROOT}static/sound/ring.mp3"></audio>

{include file="inc/footer.tpl"}
