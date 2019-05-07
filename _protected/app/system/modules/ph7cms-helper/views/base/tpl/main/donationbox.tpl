<div class="col-md-12">
    <div id="box_block" class="center">
        <h1 class="underline">😍 {lang 'Make Your Website Better!'} ❤️</h1>

        <form action="{form_action}" method="post">
            {form_body}
            <input type="image" name="submit" src="{url_tpl_mod_img}paypal-donate.en.png" alt="{lang 'Donate'}" />
        </form>

        <p>{lang '~OR~'}</p>

        <p class="s_bMarg">
            {if mt_rand(0,1) === 1} {* gives random boolean *}
                <a class="bold" href="{% $config->values['module.setting']['patreon.link'] %}" rel="noreferrer">
                    {lang 'Become a Patron!'}
                </a>
            {else}
                {{ $patreon_btns = ['become-patreon.en.png', 'support-patreon.en.png'] }}
                {{ $patreon_btn = $patreon_btns[mt_rand(0,1)] }}

                <a href="{% $config->values['module.setting']['patreon.link'] %}" rel="noreferrer">
                    <img class="img-rounded" src="{url_tpl_mod_img}{patreon_btn}" alt="Patreon" />
                </a>
            {/if}
        </p>
    </div>

    <p class="center">
        {lang}Will You Be Nice Today...?{/lang}<br />
        <span class="bold">
            {lang}And make your website better with regular updates.{/lang}
        </span><br />
        <span class="underline">
            {lang}Be like the 81% of users who contribute to the software on a regular basis 🙏{/lang}
        </span>
    </p>

    <figure class="center">
        <img src="{url_tpl_mod_img}eye.svg" alt="{lang 'Staring Eye'}" />
        <figcaption>
            <em>{lang '👀 We believe in you!'}</em>
        </figcaption>
    </figure>
</div>
