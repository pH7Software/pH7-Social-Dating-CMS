<div class="col-md-12">
    <div id="box_block" class="center">
        <h1>
            {lang "Follow <a href='%0%'>pH7CMS's Author</a> on Github!", $config->values['module.setting']['github.author_profile_link']}
        </h1>

        <p>&nbsp;</p>

        <iframe
            src="https://ghbtns.com/github-btn.html?user={% $config->values['module.setting']['github.author_username'] %}&amp;type=follow&amp;count=true&amp;size=large"
            frameborder="0"
            scrolling="0"
            width="210px"
            height="30px"
        >
        </iframe>
    </div>
</div>
