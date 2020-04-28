<div class="center">
    {if !empty($births)}
        {each $birth in $births}
            <div class="s_photo">
                {{ $avatarDesign->get($birth->username, $birth->firstName, $birth->sex, 64, $bRollover = true) }}
            </div>
        {/each}
        {main_include 'page_nav.inc.tpl'}
    {else}
        <p>{lang}No users who have a birthday today. Come back tomorrow! 😉{/lang}</p>
    {/if}
</div>
