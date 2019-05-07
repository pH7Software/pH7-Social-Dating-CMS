<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>
            {* gives random boolean *}
            {if mt_rand(0,1) === 1}
                {lang '😡 Upset? 🔨'}
            {else}
                {lang '👺 Angry? 😠'}
            {/if}
        </h1>

        <form action="{form_action}" method="post">
            {form_body}
            <input type="image" name="submit" src="{url_tpl_mod_img}paypal-donate.en.png" alt="Contribute" />
        </form>

        <div class="small">
            <ol>
                <li>
                    {lang 'Not totally satisfied with the software?'} {lang 'Nowadays, competitors are so strong, and you alone, cannot do much?'}
                </li>

                <li>
                    {lang 'You think you will never succeed with your website?'}
                </li>

                <li>
                    {lang 'You want something (much much) better...?'}
                </li>
            </ol>

            <p>{lang 'Well... you are not alone! I totally understand you!'}</p>

            <p>
                {lang '...if you want to help me making the software (and so, your website), the best on the market.'}<br />
                {lang 'And impress the big sharks out there.'}<br />
                {lang 'would you be kind to make a contribution and be part of this amazing project?'}
            </p>
        </div>
    </div>
</div>
