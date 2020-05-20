<ol id="toc">
    <li>
        <a href="#edit">
            <span>{lang 'Edit'}</span>
        </a>
    </li>
    <li>
        <a href="#avatar">
            <span>{lang 'Profile Photo'}</span>
        </a>
    </li>
    <li>
        <a href="#design">
            <span>{lang 'Profile Wallpaper'}</span>
        </a>
    </li>
    <li>
        <a href="#notification">
            <span>{lang 'Email Notification'}</span>
        </a>
    </li>
    <li>
        <a href="#privacy">
            <span>{lang 'Privacy'}</span>
        </a>
    </li>
    <li>
        <a href="{{ $design->url('payment','main','info') }}">
            <span>{lang 'Membership Details'}</span>
        </a>
    </li>
    <li>
        <a href="#pwd">
            <span>{lang 'Password'}</span>
        </a>
    </li>
</ol>

<div class="content" id="edit">
    {manual_include 'edit.tpl'}
</div>

<div class="content" id="avatar">
    {manual_include 'avatar.tpl'}
</div>

<div class="content" id="design">
    {manual_include 'design.tpl'}
</div>

<div class="content" id="notification">
    {manual_include 'notification.tpl'}
</div>

<div class="content" id="privacy">
    {manual_include 'privacy.tpl'}
</div>

<div class="content" id="pwd">
    {manual_include 'password.tpl'}
</div>

<script src="{url_static}js/tabs.js"></script>
<script>
    tabs('p', ['edit','avatar','design','notification','privacy','pwd']);
</script>
