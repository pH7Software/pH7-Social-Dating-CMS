{* If Splash Video Background if enabled in the admin panel *}
{if $is_bg_video}
    {* Count the number of different splash videos *}
    {{ $total_videos = count(glob(PH7_PATH_TPL . PH7_TPL_NAME . '/file/splash/*_vid.jpg')) }}
    {{ $i = mt_rand(1,$total_videos) }}

    {* Enable the video only if visitors aren't from a mobile devices (for performance optimization) *}
    {if !$browser->isMobile()}
        <style scoped="scoped">video#bgvid{background: url({url_tpl}file/splash/{i}_vid.jpg) no-repeat}</style>
        <video autoplay loop muted poster="{url_tpl}file/splash/{i}_vid.jpg" id="bgvid">
            <source src="{url_tpl}file/splash/{i}_vid.webm" type="video/webm" />
            <source src="{url_tpl}file/splash/{i}_vid.mp4" type="video/mp4" />
        </video>
    {else}
        <style scoped="scoped">
            body {
                background: url('{url_tpl}file/splash/{i}_vid.jpg') repeat-y;
                background-size: cover;
                top: 50%;
                left: 50%;
            }
        </style>
    {/if}
{/if}

<div class="col-md-8 login_block animated fadeInDown">
    {{ LoginSplashForm::display() }}
</div>

{if !$is_mobile}
    <div class="left col-md-8 animated fadeInLeft">
        {manual_include 'user_promo_block.inc.tpl'}
    </div>
{/if}

<div class="left col-md-4 animated fadeInRight">
    <h1 class="red3 italic underline">{headline}</h1>

    {* For small devices, the following will be activated through /templates/themes/base/css/splash.css *}
    <div class="login_button hidden center">
        <a href="{{ $design->url('user','main','login') }}" class="btn btn-primary btn-lg">
            <strong>{lang 'Login'}</strong>
        </a>
    </div>

    {{ JoinForm::step1() }}

    {if $is_mobile}
        <div class="s_tMarg"></div>
        {manual_include 'user_promo_block.inc.tpl'}
    {/if}
</div>
