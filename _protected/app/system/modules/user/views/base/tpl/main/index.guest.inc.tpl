<div class="right col-lg-4 col-md-4 col-sm-5 col-xs-10 animated fadeInRight">
    <h1 class="red3 italic underline">{headline}</h1>
    <div class="center">
        <a href="{{ $design->url('user','main','login') }}" class="btn btn-primary btn-lg">
            <strong>{lang 'Login'}</strong>
        </a>
    </div>
    {{ JoinForm::step1() }}

    <div class="counter center">
        <h2 class="red3">{lang 'People love us! Realtime users using our service'}</h2>
        {{ $userDesign->userCounter() }}
    </div>
</div>

<div class="left col-lg-7 col-md-8 col-sm-7 col-xs-12">
    <div class="folio_block animated fadeInDown">
        <h1 class="red3 italic underline">{slogan}</h1>

        <div class="splash_slideshow">
            <div class="window">
                <div class="img_reel">
                    {for $i in [1,2,3,4,5]}
                        <a href="{url_root}">
                            <img src="{url_tpl_img}slideshow/{i}.jpg" alt="{lang 'Social Dating Web App'}" />
                        </a>
                    {/for}
                </div>
            </div>
            <div class="paging">
                <a href="#" rel="1">1</a>
                <a href="#" rel="2">2</a>
                <a href="#" rel="3">3</a>
                <a href="#" rel="4">4</a>
                <a href="#" rel="5">5</a>
            </div>
        </div>
    </div>

    <div class="block_txt">
        <h2>{lang '🚀 Meet amazing people near %0%! 🎉', $design->geoIp(false)}</h2>
        {promo_text}
    </div>

    {if $is_users_block}
        <div class="carousel animated fadeInLeft">
            {{ $userDesignModel->carouselProfiles() }}
        </div>
    {/if}
</div>
