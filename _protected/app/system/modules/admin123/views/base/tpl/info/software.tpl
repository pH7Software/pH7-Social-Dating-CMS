<div class="center">
    <h2 class="underline">{lang 'Let others know about pH7CMS'}</h2>
    <p class="italic">{lang 'Here is a <a class="bold" href="%0%">pre-written Tweet</a> (which you can edit, of course).', $tweet_msg}</p>

    <div class="s_bMarg"></div>

    <h2 class="underline">{lang 'Software Information'}</h2>
    <div class="italic">
        <p>{lang 'Name: %0%', '<a href="%software_website%">%software_name%</a>'}</p>
        <p>Github: <a href="https://github.com/pH7Software/pH7-Social-Dating-CMS">https://github.com/pH7Software/pH7-Social-Dating-CMS</a></p>
        <p>{lang 'Author: %0%', '<a href="http://ph7.me">Pierre-Henry Soria</a>'}</p>
    </div>

    <div class="s_bMarg"></div>

    <h2 class="underline">{lang 'Software Version'}</h2>
    <div class="italic">
        <p>{lang 'Version: %software_version%'}</p>
        <p>{lang 'Version Name: %software_version_name%'}</p>
        <p>{lang 'Version Build: %software_build%'}</p>
        <p>{lang 'Release Date: %0%', $release_date}</p>
    </div>

    <div class="s_bMarg"></div>

    <p><a class="bold" href="{{ $design->url('ph7cms-helper','main','suggestionbox','?box=donationbox') }}">{lang 'Contribute to pH7CMS'}</a></p>
    <p><a class="underline" href="https://sourceforge.net/p/ph7socialdating">{lang 'Give a Nice Review on Sourceforge'}</a></p>
</div>
