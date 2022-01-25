<div class="col-md-12">
    <div id="box_block" class="center">
        <h3>
            {lang 'Are you Feeling Generous Enough? Will you <a href="%0%">Give a Nice Review</a>?', $config->values['module.setting']['review.link']}
        </h3>

        <figure class="center">
            <a href="{% $config->values['module.setting']['review.link'] %}">
                <img
                    src="{url_tpl_mod_img}review.svg"
                    alt="Sourceforge Reviews"
                    title="{lang 'Leave a Review'}"
                />
            </a>
            <figcaption>
                <em>{lang 'Really appreciate! 😊'}</em>
            </figcaption>
        </figure>
    </div>
</div>
