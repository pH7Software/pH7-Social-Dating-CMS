<div class="center">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <h2 class="panel-heading underline">{lang 'Software Information'}</h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        {lang 'Name: %0%', '<a href="%software_website%">%software_name%</a>'}
                    </li>
                    <li class="list-group-item">
                        {lang 'Source: %0%', '<a href="https://github.com/pH7Software/pH7-Social-Dating-CMS">GitHub repository</a>'}
                    </li>
                    <li class="list-group-item">
                        {lang 'Organization: %0%', '<a href="https://github.com/pH7Software">pH7Software</a>'}
                    </li>
                    <li class="list-group-item">
                        {lang 'Creator: %0%', '<a href="https://ph7.me">Pierre-Henry Soria</a> (<a href="https://github.com/pH-7">GitHub</a>)'}
                    </li>
                    <li class="list-group-item">
                        {lang 'License: %0%', '<a href="https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/LICENSE.md">MIT</a>'}
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel panel-default">
            <h2 class="panel-heading underline">{lang 'Software Version'}</h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">{lang 'Version: %software_version%'}</li>
                    <li class="list-group-item">{lang 'Version Name: %software_version_name%'}</li>
                    <li class="list-group-item">{lang 'Version Build: %software_build%'}</li>
                    <li class="list-group-item">{lang 'Release Date: %0%', $release_date}</li>
                </ul>
            </div>
        </div>

        <div class="panel panel-default">
            <h2 class="panel-heading underline">{lang 'Project Links'}</h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS/tree/18.x/docs">{lang 'Release guides'}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS/issues">{lang 'Issue tracker'}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS/discussions">{lang 'Community discussions'}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/CONTRIBUTING.md">{lang 'Contributing guide'}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="{{ $design->url('ph7cms-helper', 'main', 'suggestionbox', '?box=donationbox') }}">{lang 'Optional project support'}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
