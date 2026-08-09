<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>{lang 'Support pH7Builder'}</h1>

        <p>
            {lang 'pH7Builder is MIT-licensed open-source software. Financial support is optional and helps fund continued maintenance.'}
        </p>

        <form action="{form_action}" method="post">
            {form_body}
            <input type="image" name="submit" src="{url_tpl_mod_img}paypal-donate.en.png" alt="{lang 'Donate with PayPal'}" />
        </form>

        <p>{lang 'Other optional ways to support the project:'}</p>
        <p>
            <a href="{% $config->values['module.setting']['buymeacoffee.link'] %}" target="_blank" rel="noopener noreferrer">
                {lang 'Buy Me a Coffee'}
            </a>
            &nbsp;·&nbsp;
            <a href="{% $config->values['module.setting']['patreon.link'] %}" target="_blank" rel="noopener noreferrer">
                {lang 'Patreon'}
            </a>
        </p>

        <p><small>{lang 'Thank you for using and contributing to pH7Builder. There is no obligation to donate.'}</small></p>
    </div>
</div>
