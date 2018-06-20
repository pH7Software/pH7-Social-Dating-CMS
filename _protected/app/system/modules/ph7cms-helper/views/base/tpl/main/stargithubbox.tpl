<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>{lang 'Star pH7CMS on <a href="%0%">Github</a>!', $config->values['module.setting']['github.repository_link']}</h1>

        <p>&nbsp;</p>

        <iframe
            src="https://ghbtns.com/github-btn.html?user={% $config->values['module.setting']['github.username'] %}&repo={% $config->values['module.setting']['github.repo_name'] %}&type=star&count=true&size=large"
            frameborder="0"
            scrolling="0"
            width="160px"
            height="30px"
        >
        </iframe>
    </div>
</div>
