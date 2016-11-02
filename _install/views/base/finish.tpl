{include file="inc/header.tpl"}

<h2>{$LANG.finish}</h2>

<p class="bold">{$LANG.looking_hosting}</p>
<p class="bold"><a href="{$smarty.const.PH7_URL_ROOT}" target="_blank">{$LANG.go_your_site}</a> &nbsp; | &nbsp; <a href="{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}" target="_blank">{$LANG.go_your_admin_panel} (<em>{$smarty.const.PH7_URL_ROOT}{$smarty.const.PH7_ADMIN_MOD}</em>)</a></p>
<p>&nbsp;</p>

<p>{$LANG.remove_install_folder}</p>
<form action="{$smarty.const.PH7_URL_SLUG_INSTALL}finish" method="post">
    <p><button class="button" type="submit" name="confirm_remove_install" value="1" onclick="return confirm('{$LANG.confirm_remove_install_folder_auto}')">{$LANG.remove_install_folder_auto}</button></p>
</form>

{include file="inc/footer.tpl"}
