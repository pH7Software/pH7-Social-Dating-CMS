<div class="row">
    <div class="left col-md-3">
        <h3>{lang 'My Profile Photo'}</h3>
        {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

        <ul>
            <li>
                <a href="{{ $design->url('user','setting','avatar') }}" title="{lang 'Change My Profile Photo'}"><i class="fa fa-upload"></i> {lang 'Change Profile Photo'}</a>
            </li>
            <li>
                <a href="{{ $design->url('user','setting','edit') }}" title="{lang 'Edit My Profile'}"><i class="fa fa-cog fa-fw"></i> {lang 'Edit Profile'}</a>
            </li>
            <li>
                <a href="{{ $design->url('user','setting','design') }}" title="{lang 'My Wallpaper'}"><i class="fa fa-picture-o"></i> {lang 'Design Profile'}</a></li>
            <li>
                <a href="{{ $design->url('user','setting','notification') }}" title="{lang 'My Email Notification Settings'}"><i class="fa fa-envelope-o"></i> {lang 'Notifications'}</a>
            </li>
            <li>
                <a href="{{ $design->url('user','setting','privacy') }}" title="{lang 'My Privacy Settings'}"><i class="fa fa-user-secret"></i> {lang 'Privacy Setting'}</a>
            </li>
            {if $is_valid_license}
                <li>
                    <a href="{{ $design->url('payment','main','info') }}" title="{lang 'My Membership'}"><i class="fa fa-credit-card"></i> {lang 'Membership Details'}</a>
                </li>
            {/if}
            <li><a href="{{ $design->url('user','setting','password') }}" title="{lang 'Change My Password'}"><i class="fa fa-key fa-fw"></i> {lang 'Change Password'}</a></li>
        </ul>
    </div>

    <div class="left col-md-6">
        <h3 class="center underline">{lang 'The latest users'}</h3>
        {{ $userDesignModel->profilesBlock() }}

        <h3 class="center underline">{lang 'Visitors who visited my profile'}</h3>
        <div class="content" id="visitor">
            <script>
                var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
                $('#visitor').load(url_visitor_block + ' #visitor_block');
            </script>
        </div>
    </div>

    <div class="left col-md-3">
        <h3>{lang 'The latest news'}</h3>
        <div id="wall"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('ul.zoomer_pic').slick({
            dots: true,
            infinite: true,
            slidesToShow: 6,
            adaptiveHeight: true
        })
    });
</script>
