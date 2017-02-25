<div class="center" id="related_profile_block">
    {if empty($error)}
        {each $profile in $related_profiles}
            <div class="s_photo">
                {{ $avatarDesign->get($profile->username, $profile->firstName, $profile->sex, 64, $bRollover = true) }}
            </div>
        {/each}
    {else}
        <p>{error}</p>
    {/if}
</div>