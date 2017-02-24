<div class="center" id="related_profile_block">
    {each $profile in $related_profiles}
        {{ $avatarDesign->get($profile->username, $profile->firstName, $profile->sex, 64, $bRollover = true) }}
    {/each}
</div>