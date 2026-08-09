{include file="inc/header.tpl"}

<h2>{$LANG.config_site}</h2>

{include file="inc/errors.tpl"}

<form method="post" action="{$smarty.const.PH7_URL_SLUG_INSTALL}config_site">
    <input type="hidden" name="action_token" value="{$action_token|escape}" />
    <fieldset>
        <p>
            <span class="mandatory">*</span> <label for="site_name">{$LANG.site_name}:</label><br />
            <input
                type="text"
                name="site_name"
                id="site_name"
                onfocus="if('{$def_site_name}' == this.value) this.value='';"
                onblur="if ('' == this.value) this.value = '{$def_site_name}';"
                value="{$smarty.session.val.site_name|escape}"
                minlength="2"
                maxlength="50"
                required="required"
            />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_username">{$LANG.admin_username}:</label><br />
            <span class="small italic">{$smarty.const.PH7_URL_ROOT}<strong>{$smarty.const.PH7_ADMIN_MOD}</strong></span><br />
            <input type="text" name="admin_username" id="admin_username" onfocus="if('{$def_admin_username}' == this.value) this.value='';" onblur="if ('' == this.value) this.value = '{$def_admin_username}';" value="{$smarty.session.val.admin_username|escape}" minlength="3" maxlength="30" autocomplete="username" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_login_email">{$LANG.admin_login_email}:</label><br />
            <span class="small italic">{$smarty.const.PH7_URL_ROOT}<strong>{$smarty.const.PH7_ADMIN_MOD}</strong></span><br />
            <input type="email" name="admin_login_email" id="admin_login_email" value="{$smarty.session.val.admin_login_email|escape}" maxlength="120" autocomplete="email" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_password">{$LANG.admin_password}:</label><br />
            <input type="password" name="admin_password" id="admin_password" minlength="12" maxlength="92" autocomplete="new-password" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_passwords">{$LANG.admin_passwords}:</label><br />
            <input type="password" name="admin_passwords" id="admin_passwords" minlength="12" maxlength="92" autocomplete="new-password" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_first_name">{$LANG.admin_first_name}:</label><br />
            <input type="text" name="admin_first_name" id="admin_first_name" value="{$smarty.session.val.admin_first_name|escape}" minlength="2" maxlength="20" autocomplete="given-name" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_last_name">{$LANG.admin_last_name}:</label><br />
            <input type="text" name="admin_last_name" id="admin_last_name" value="{$smarty.session.val.admin_last_name|escape}" minlength="2" maxlength="20" autocomplete="family-name" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_email">{$LANG.admin_email}:</label><br />
            <input type="email" name="admin_email" id="admin_email" value="{$smarty.session.val.admin_email|escape}" placeholder="{$LANG.admin_email_placeholder}" maxlength="120" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_feedback_email">{$LANG.admin_feedback_email}:</label><br />
            <input type="email" name="admin_feedback_email" id="admin_feedback_email" value="{$smarty.session.val.admin_feedback_email|escape}" placeholder="{$LANG.admin_feedback_email_placeholder}" maxlength="120" required="required" />
        </p>

        <p>
            <span class="mandatory">*</span> <label for="admin_return_email">{$LANG.admin_return_email}:</label><br />
            <input type="email" name="admin_return_email" id="admin_return_email" value="{$smarty.session.val.admin_return_email|escape}" placeholder="{$LANG.admin_return_email_placeholder}" maxlength="120" required="required" />
        </p>

        <p>
            <input type="checkbox" name="sample_data_request" id="sample_data_request" /> <label for="sample_data_request">{$LANG.add_sample_data}</label>
        </p>

        <p>
            <button type="submit" name="config_site_submit" value="1" class="btn btn-primary btn-lg">{$LANG.next}</button>
        </p>
    </fieldset>
</form>

{include file="inc/footer.tpl"}
