{include file="inc/header.tpl"}

<h2>{$LANG.finish}</h2>

<p class="bold"><a href="{$smarty.const.PH7_URL_ROOT}" target="_blank">{$LANG.go_your_site}</a> &nbsp; | &nbsp; <a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank">{$LANG.go_your_admin_panel}</a> (<a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank"><em class="text-info">{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}</em></a>)</p>

{if !empty($admin_login_email) && !empty($admin_username)}
    <ul>
        <li>{$LANG.admin_url}: <a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank"><strong class="text-info">{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}</strong></a></li>
        <li>{$LANG.admin_login_email}: <strong class="text-info">{$admin_login_email|escape}</strong></li>
        <li>{$LANG.admin_username}: <strong class="text-info">{$admin_username|escape}</strong></li>
    </ul>
{/if}

<p>
    {$LANG.will_you_make_donation} <a href="{$patreon_url}" data-patreon-widget-type="become-patron-button" target="_blank" rel="noopener noreferrer">{$LANG.donate_here}</a>.<br />
    <span class="small"><a href="{$paypal_donate_url}" target="_blank" rel="noopener noreferrer">{$LANG.or_paypal_donation}</a></span>
</p>

<p>&nbsp;</p>

<p>{$LANG.remove_install_folder}</p>
<form action="{$smarty.const.PH7_URL_SLUG_INSTALL}finish" method="post">
    <p><button class="button" type="submit" name="confirm_remove_install" value="1" onclick="return confirm('{$LANG.confirm_remove_install_folder_auto}')">{$LANG.remove_install_folder_auto}</button></p>
</form>

<script async="async" src="https://c6.patreon.com/becomePatronButton.bundle.js"></script>

{include file="inc/footer.tpl"}
