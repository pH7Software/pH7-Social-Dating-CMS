{include file="inc/header.tpl"}

<h2>{$LANG.finish}</h2>

<p>{$LANG.remove_install_folder}</p>
<form action="{$smarty.const.PH7_URL_SLUG_INSTALL}finish" method="post">
    <p><button type="submit" name="confirm_remove_install" value="1" onclick="return confirm('{$LANG.confirm_remove_install_folder_auto}')">{$LANG.remove_install_folder_auto}</button></p>
</form>

<p>&nbsp;</p>

<p class="bold"><a href="{$smarty.const.PH7_URL_ROOT}">{$LANG.go_your_site}</a> &nbsp; | &nbsp; <a href="{$smarty.const.PH7_URL_ROOT}admin123">{$LANG.go_your_admin_panel}</a></p>

{include file="inc/footer.tpl"}
