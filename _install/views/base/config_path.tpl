{include file="inc/header.tpl"}

<h2>{$LANG.config_path}</h2>

{include file="inc/errors.tpl"}

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}config_path">
    <fieldset>
        <p>
            <span class="mandatory">*</span> <label for="path_protected">{$LANG.path_protected}:</label><br />
            <span class="small">{$LANG.desc_config_path}</span><br />
            <input type="text" name="path_protected" id="path_protected" value="{$smarty.session.val.path_protected|escape}" required="required" />
        </p>

        <div id="txtDir"></div>

        <p>
            <button type="submit" class="btn btn-primary btn-lg">{$LANG.next}</button>
        </p>
    </fieldset>
</form>

<script src="{$smarty.const.PH7_URL_INSTALL}static/js/AJAPH.js"></script>
{literal}
    <script>
        var oCheckDir = document.getElementById("path_protected");
        oCheckDir.onkeyup = function () {
            var sDir = oCheckDir.value, sHtmlId = "txtDir";
            if (sDir == "") {
                document.getElementById(sHtmlId).innerHTML = "";
                return;
            }

            (new AJAPH).send("POST", sInstallUrl + "inc/ajax/check_dir.php", "dir=" + sDir).setResponseHtml(sHtmlId);
        }
    </script>
{/literal}

{include file="inc/footer.tpl"}
