<h1 class="red3 italic underline s_bMarg">{slogan}</h1>
{if $is_users_block}
    <div class="center profiles_window thumb pic_block">
        {{ $userDesignModel->profiles(0, $number_profiles) }}
    </div>
{/if}

<div class="s_tMarg" id="promo_text">
    <h2>{lang 'Meet people near %0% who share your interests', $design->geoIp(false)}</h2>
    {promo_text}
</div>
