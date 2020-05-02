<ol id="toc">
    <li><a href="#general"><span>{lang 'General Settings'}</span></a></li>
    <li><a href="#icon"><span>{lang 'Logo'}</span></a></li>
    <li><a href="#design"><span>{lang 'Design (colors)'}</span></a></li>
    <li><a href="#registration"><span>{lang 'Registration'}</span></a></li>
    {if $is_video_enabled OR $is_picture_enabled}
        <li><a href="#pic_vid"><span>{lang 'Picture and Video'}</span></a></li>
    {/if}
    <li><a href="#moderation"><span>{lang 'Moderation'}</span></a></li>
    <li><a href="#email"><span>{lang 'Email'}</span></a></li>
    <li><a href="#security"><span>{lang 'Security'}</span></a></li>
    <li><a href="#spam"><span>{lang 'Spam'}</span></a></li>
    <li><a href="#api"><span>{lang 'API'}</span></a></li>
    <li><a href="#automation"><span>{lang 'Automation'}</span></a></li>
</ol>
{{ SettingForm::display() }}

<script>
    /* Check if the Setting page is loading from 'p=registration'
     * If so, scroll down to show the "Default Membership Group" first (this is used by the Payment module) */
    var sHash = location.hash.substr(1);
    if (sHash === 'p=registration') {
        var $target = $('html, body');
        $target.animate({scrollTop: $target.height()}, 1000);
    }
</script>
