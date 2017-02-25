<div class="center" id="related_profile_block">
    {if empty($error)}
        {each $profile in $related_profiles}
            {if $id !== $profile->profileId}
                <div class="s_photo">
                    {{ $avatarDesign->get($profile->username, $profile->firstName, $profile->sex, 64, $bRollover = true) }}
                </div>
            {else}
                <p>{lang 'No related profiles found.'}</p>
            {/if}
        {/each}
    {else}
        <p>{error}</p>
    {/if}
</div>