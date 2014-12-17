{include file="inc/header.tpl"}

<h2>{$LANG.license}</h2>

<p class="warning">{$LANG.license_desc}</p>
<p><iframe src="{$software_license_url}?l={$current_lang}">{$LANG.need_frame}</iframe></p>

{include file="inc/errors.tpl"}

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}license" name="lic">

    <p><label for="license">{$LANG.your_license}</label><br />
    {$LANG.registration_for_license}<br />
    <input type="text" name="license" onkeypress="return checkInput(event)" id="license" value="{$smarty.session.val.license|escape}" autocomplete="off" required="required" /></p>

    <div id="txtLic"></div>

    <p><input type="checkbox" name="chk" id="agree" onclick="checkLic()" /> <label for="agree">{$LANG.agree_license}</label></p>

    <p><input type="submit" name="sbmt" disabled="disabled" value="{$LANG.next}" /></p>

</form>

<script src="{$smarty.const.PH7_URL_INSTALL}static/js/AJAPH.js"></script>
{literal}
<script>
function checkLic()
{
    document.lic.sbmt.disabled = (document.lic.chk.checked) ? false : true;
}

function checkInput(oEvt)
{
    var iUnicode = oEvt.charCode? oEvt.charCode : oEvt.keyCode;
    if(iUnicode == 32) return false; // Do not allow the space key
}

var oCheckLic = document.getElementById("license");
oCheckLic.onkeyup = function()
{
  var sLic = oCheckLic.value, sHtmlId = "txtLic";
  if(sLic == "")
  {
    document.getElementById(sHtmlId).innerHTML = "";
    return;
  }

  (new AJAPH).send("POST", sInstallUrl + "inc/ajax/check_lic.php", "lic=" + sLic).setResponseHtml(sHtmlId);
}
</script>
{/literal}

{include file="inc/footer.tpl"}
