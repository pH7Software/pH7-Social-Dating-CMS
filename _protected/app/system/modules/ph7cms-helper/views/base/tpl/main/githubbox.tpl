<div class="col-md-12">
    <div id="box_block" class="center">
        <h2>
            {lang '<a href="%0%">View pH7Builder</a> on GitHub', $config->values['module.setting']['github.repository_link']}
        </h2>

        <figure class="center">
            <a href="{% $config->values['module.setting']['github.repository_link'] %}">
                <img
                    src="{url_tpl_mod_img}github.svg"
                    alt="pH7Builder on GitHub"
                    title="{lang 'View pH7Builder on GitHub'}"
                />
            </a>
            <figcaption>
                <em>{lang 'Source code, issues, and contributions'}</em>
            </figcaption>
        </figure>
    </div>
</div>
