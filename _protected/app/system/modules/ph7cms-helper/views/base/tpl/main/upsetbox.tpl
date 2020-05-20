<div class="col-md-12">
    <div class="center s_bMarg">
        <h1>
            {* gives random boolean *}
            {if mt_rand(0,1) === 1}
                {lang '😡 Upset? 🔨'}
            {else}
                <span class="underline">
                    {lang '👺 Angry?! 😡'}
                </span>
            {/if}
        </h1>

        <ul>
            <li>
                {lang 'Not totally satisfied with the software?'} {lang 'Nowadays, competitors are so strong, and you alone, cannot do much?'}
            </li>

            <li>
                {lang 'You think you will never succeed with your current website?'}
            </li>

            <li>
                {lang 'You want something (much much) better...?'}
            </li>
        </ul>

        <p>{lang 'Well... you are not alone! I totally understand you!'}</p>
        <p>
            {lang '...if you want to help me making the software (and so, your website) the best on the market.'}<br />
            {lang 'And impress the big sharks out there! 🦈'}<br />
            {lang 'Would you be willing to give a contribution? And being part of this amazing project?'}
        </p>
        <p class="s_tMarg">
            {lang 'Once you made a donation, I will ship faster and better to you new features and changes to the software 🚀'}
        </p>
    </div>

    <div id="box_block" class="center">
        <h2 class="italic">🚀 {lang 'YES!! Make It Much Better!!!'} ⏰️</h2>

        <p>
            {if mt_rand(0,1) === 1}
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

        <form action="{form_action}" method="post">
            {form_body}
            <input type="image" name="submit" src="{url_tpl_mod_img}paypal-donate.en.png" alt="{lang 'Contribute'}" />
        </form>
    </div>
</div>
