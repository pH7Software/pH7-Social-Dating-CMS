<div class="center">
    {if $config->values['module.api']['facebook.enabled']}
        <a href="{{ $design->url('connect','main','login','fb') }}" rel="nofollow" target="_blank">
            <img src="{url_tpl_mod_img}facebook.svg" alt="Facebook Connect" />
        </a>  &nbsp; &nbsp;
    {/if}

    {if $config->values['module.api']['google.enabled']}
        <a href="{{ $design->url('connect','main','login','google') }}" rel="nofollow" target="_blank">
            <img src="{url_tpl_mod_img}google.svg" alt="Google Connect" />
        </a>  &nbsp; &nbsp;
    {/if}

    {if $config->values['module.api']['twitter.enabled']}
        <a href="{{ $design->url('connect','main','login','twitter') }}" rel="nofollow" target="_blank">
            <img src="{url_tpl_mod_img}twitter.svg" alt="Twitter Connect" />
        </a>  &nbsp; &nbsp;
    {/if}

    {if $config->values['module.api']['microsoft.enabled']}
        <a href="{{ $design->url('connect','main','login','microsoft') }}" rel="nofollow" target="_blank">
            <img src="{url_tpl_mod_img}microsoft.svg" alt="Microsoft" />
        </a>
    {/if}
</div>
