<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>
            {lang '<a href="%0%">Fork pH7CMS</a> on Github!', $config->values['module.setting']['github.repository_link']} 🚀
        </h1>

        <p>&nbsp;</p>

        <iframe
            src="https://ghbtns.com/github-btn.html?user={% $config->values['module.setting']['github.username'] %}&amp;repo={% $config->values['module.setting']['github.repo_name'] %}&amp;type=fork&amp;count=true&amp;size=large"
            frameborder="0"
            scrolling="0"
            width="158px"
            height="30px"
        >
        </iframe>
    </div>
</div>
