<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>{lang 'Sex!'} ❤️</h1>

        <p>
            {lang 'Sorry, just need your attention 😉'} {lang "Don't forget to <a href='%0%'>'Star'</a> the software you ❤️", $config->values['module.setting']['github.repository_link']}
        </p>

        <iframe
            src="https://ghbtns.com/github-btn.html?user={% $config->values['module.setting']['github.username'] %}&amp;repo={% $config->values['module.setting']['github.repo_name'] %}&amp;type=star&amp;count=true&amp;size=large"
            frameborder="0"
            scrolling="0"
            width="160px"
            height="30px"
        >
        </iframe>
    </div>
</div>
