<div class="center">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <h2 class="panel-heading underline">
                {lang 'Let others know about pH7CMS 🤗'}
            </h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        {lang 'Here is a <a class="bold underline" href="%0%">pre-written Tweet</a> (which you can edit, of course).', $tweet_msg_url}
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel panel-default">
            <h2 class="panel-heading underline">{lang 'Software Information 👀'}</h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        {lang 'Name: %0%', '<a href="%software_website%">%software_name%</a>'}
                    </li>
                    <li class="list-group-item">
                        Github: <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS">
                            https://github.com/pH7Software/pH7-Social-Dating-CMS
                        </a>
                    </li>
                    <li class="list-group-item">
                        {lang 'Author: %0%', '<a href="https://ph7.me">Pierre-Henry Soria</a>'}
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel panel-default">
            <h2 class="panel-heading underline">{lang 'Software Version ✅'}</h2>
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
            <h2 class="panel-heading underline">{lang 'Help %software_name% 🏆'}</h2>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a class="bold" href="{{ $design->url('ph7cms-helper','main','suggestionbox','?box=donationbox') }}">
                            {lang 'Contribute to %software_name% 💪'}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a class="underline" href="https://sourceforge.net/p/ph7socialdating">
                            {lang 'Give a Nice Review on Sourceforge 💫'}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
