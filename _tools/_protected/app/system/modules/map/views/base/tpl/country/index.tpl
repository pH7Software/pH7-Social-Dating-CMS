<div class="center">
    {if empty($error)}
        <div>{map}</div>
        {{ $userDesignModel->geoProfiles($country_code, $city, $first_user, $nb_user_by_page) }}
        {main_include 'page_nav.inc.tpl'}
    {else}
        <p class="red">
            {error}
        </p>
    {/if}
</div>
