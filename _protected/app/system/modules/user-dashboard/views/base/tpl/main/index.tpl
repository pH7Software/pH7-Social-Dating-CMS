<div class="row">
    {* "My Profile" block don't really fit well on small mobile devices, so ignore it if it's the case *}
    {if !$browser->isMobile()}
        <div class="left col-xs-12 col-sm-4 col-md-3">
            <h2>{lang 'My %0% Profile', $user_type}</h2>
            {if $sex !== 'buyer'}
                {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}
            {/if}

            <ul>
                {if $sex !== 'buyer'}
                    <li>
                        <a href="{{ $design->url('realestate','setting','avatar') }}" title="{lang 'Change My Main Photo'}">
                            <i class="fa fa-upload"></i> {lang 'Change Main Photo'}
                        </a>
                    </li>
                {/if}
                <li>
                    <a href="{{ $design->url('realestate','setting','edit') }}" title="{lang 'Edit My Profile'}"><i class="fa fa-cog fa-fw"></i> {lang 'Edit Profile'}</a>
                </li>
                <li>
                    <a href="{{ $design->url('realestate','setting','notification') }}" title="{lang 'My Email Notification Settings'}"><i class="fa fa-envelope-o"></i> {lang 'Notifications'}</a>
                </li>
                <li>
                    <a href="{{ $design->url('realestate','setting','privacy') }}" title="{lang 'My Privacy Settings'}"><i class="fa fa-user-secret"></i> {lang 'Privacy Setting'}</a>
                </li>
                <li>
                    <a href="{{ $design->url('payment','main','info') }}" title="{lang 'My Membership'}"><i class="fa fa-credit-card"></i> {lang 'Membership Details'}</a>
                </li>
                <li>
                    <a href="{{ $design->url('realestate','setting','password') }}" title="{lang 'Change My Password'}"><i class="fa fa-key fa-fw"></i> {lang 'Change Password'}</a>
                </li>
            </ul>
        </div>
    {/if}

    <div class="left col-xs-12 col-sm-6 col-md-6">
        {if $sex === 'buyer' || $sex === 'both'}
            {* if profiles are buyers, show the seller form *}
            <h1 class="center underline italic s_tMarg">{lang 'Search Sellers'}</h1>
            {{ SearchSellerForm::display() }}
        {/if}

        {if $is_picture_enabled AND ($sex === 'seller' || $sex === 'both')}
            <h2 class="center underline">{lang 'My photo albums'}</h2>
            <div class="content" id="picture">
                <script>
                    var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
                    $('#picture').load(url_picture_block + ' #picture_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_video_enabled AND ($sex === 'seller' || $sex === 'both')}
            <h2 class="center underline">{lang 'My video albums'}</h2>
            <div class="content" id="video">
                <script>
                    var url_video_block = '{{ $design->url('video','main','albums',$username) }}';
                    $('#video').load(url_video_block + ' #video_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_forum_enabled}
            <h2 class="center underline">{lang 'My discussions'}</h2>
            <div class="content" id="forum">
                <script>
                    var url_forum_block = '{{ $design->url('forum','forum','showpostbyprofile',$username) }}';
                    $('#forum').load(url_forum_block + ' #forum_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_note_enabled}
            <h2 class="center underline">{lang 'My notes'}</h2>
            <div class="content" id="note">
                <script>
                    var url_note_block = '{{ $design->url('note','main','author',$username) }}';
                    $('#note').load(url_note_block + ' #note_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}
    </div>

    <div class="left col-xs-12 col-sm-2 col-md-3">
        {if $sex === 'seller' || $sex === 'both'}
            <h3>{lang 'The Latest Updates between Buyers/Sellers'}</h3>
            <div id="wall"></div>
        {/if}
    </div>
</div>

<script>
    $(document).ready(function() {
        $('ul.zoomer_pic').slick({
            dots: true,
            infinite: false,
            slidesToShow: 6,
            slidesToScroll: 6,
            adaptiveHeight: true
        })
    });
</script>
