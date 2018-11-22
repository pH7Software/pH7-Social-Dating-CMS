{include file="inc/header.tpl"}

<h2>{$LANG.config_system}</h2>

{include file="inc/errors.tpl"}

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}config_system">
    <fieldset>
        <input type="hidden" name="db_type_name" value="{$smarty.session.db.type_name|escape}" required="required" />
        <input type="hidden" name="db_type" value="{$smarty.session.db.type|escape}" required="required" />

        <p>
            <span class="mandatory">*</span> <label for="db_hostname">{$LANG.db_hostname}:</label><br />
            <span class="small">{$LANG.desc_db_hostname}</span><br />
            <input type="text" name="db_hostname" id="db_hostname" onfocus="if ('{$def_db_hostname}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_hostname}';" value="{$smarty.session.db.hostname|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_username">{$LANG.db_username}:</label><br />
            <input type="text" name="db_username" id="db_username" onfocus="if ('{$def_db_username}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_username}';" value="{$smarty.session.db.username|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_password">{$LANG.db_password}:</label><br />
            <input type="password" id="db_password" name="db_password" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_name">{$LANG.db_name}:</label><br />
            <input type="text" name="db_name" id="db_name" onfocus="if ('{$def_db_name}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_name}';" value="{$smarty.session.db.name|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_prefix">{$LANG.db_prefix}:</label><br />
            <span class="small">{$LANG.desc_db_prefix}</span><br />
            <input type="text" name="db_prefix" id="db_prefix" pattern="[\w_]+" onfocus="if ('{$def_db_prefix}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_prefix}';" value="{$smarty.session.db.prefix|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_charset">{$LANG.db_encoding}:</label><br />
            <span class="small">{$LANG.desc_db_encoding}</span><br />
            <input type="text" name="db_charset" id="db_charset" onfocus="if ('{$def_db_charset}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_charset}';" value="{$smarty.session.db.charset|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="db_port">{$LANG.db_port}:</label><br />
            <span class="small">{$LANG.desc_db_port}</span><br />
            <input type="text" name="db_port" id="db_port" onfocus="if ('{$def_db_port}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_db_port}';" value="{$smarty.session.db.port|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="ffmpeg_path">{$LANG.ffmpeg_path}:</label><br />
            <input type="text" name="ffmpeg_path" id="ffmpeg_path" value="{$smarty.session.val.ffmpeg_path|escape}" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="bug_report_email">{$LANG.bug_report_email}:</label><br />
            <input type="email" name="bug_report_email" id="bug_report_email" value="{$smarty.session.val.bug_report_email|escape}" placeholder="{$LANG.bug_report_email_placeholder}" required="required" />
        </p>

        <p>
            <button type="submit" name="config_system_submit" value="1" class="btn btn-primary btn-lg">{$LANG.next}</button>
        </p>
    </fieldset>
</form>

{include file="inc/footer.tpl"}
