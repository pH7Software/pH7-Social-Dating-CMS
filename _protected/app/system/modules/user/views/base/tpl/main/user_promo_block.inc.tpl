<h1 class="pink2 italic underline s_bMarg">{slogan}</h1>
<div class="profiles_window thumb">
    {{ $userDesignModel->profiles() }}
</div>

<div class="s_tMarg" id="promo_text">
    <h2>{lang 'Meet people in %0% with %site_name%!', $design->geoIp(false)}</h2>
    {promo_text}
</div>