<ol id="toc">
    <li><a href="#general"><span>{lang 'General Settings'}</span></a></li>
    <li><a href="#logotype"><span>{lang 'Logo'}</span></a></li>
    <li><a href="#registration"><span>{lang 'Registration'}</span></a></li>
    <li><a href="#pic_vid"><span>{lang 'Picture and Video'}</span></a></li>
    <li><a href="#moderation"><span>{lang 'Moderation'}</span></a></li>
    <li><a href="#email"><span>{lang 'Email'}</span></a></li>
    <li><a href="#security"><span>{lang 'Security'}</span></a></li>
    <li><a href="#spam"><span>{lang 'Spam'}</span></a></li>
    <li><a href="#design"><span>{lang 'Design (color)'}</span></a></li>
    <li><a href="#api"><span>{lang 'API'}</span></a></li>
    <li><a href="#automation"><span>{lang 'Automation'}</span></a></li>
</ol>
{{ SettingForm::display() }}

<script>
    /* Check if the Setting page is loading from 'p=registration'
     * If so, scroll down to show the "Default Membership Group" first (this is used by the Payment module) */
    var sHash = location.hash.substr(1);
    if (sHash == 'p=registration') {
        var $target = $('html, body');
        $target.animate({scrollTop: $target.height()}, 1000);
    }
</script>
