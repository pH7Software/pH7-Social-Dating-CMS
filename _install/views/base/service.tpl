{include file="inc/header.tpl"}

<h2>{$LANG.service}</h2>

<div class="col-md-6">
    <h3 class="underline">{$LANG.buy_copyright_license_title}</h3>
    <p><a class="button" href="{$software_license_key_url}" target="_blank">{$LANG.buy_copyright_license}</a></p>
    <p class="italic">{$LANG.buy_copyright_license_desc}</p>
</div>

<div class="col-md-6">
    <h3 class="underline">{$LANG.buy_individual_ticket_support_title}</h3>
    <p><a class="button" href="{$software_help_url}" target="_blank">{$LANG.buy_individual_ticket_support}</a></p>
    <p class="italic">{$LANG.buy_individual_ticket_support_desc}</p>
</div>

<div class="clear"></div>
<p><button type="button" onclick="window.location='{$smarty.const.PH7_URL_SLUG_INSTALL}license'" class="btn btn-primary">{$LANG.later}</button></p>

{include file="inc/footer.tpl"}
