<div class="center" id="related_profile_block">
    {if !empty($related_profiles)}
        {each $profile in $related_profiles}
            {if $id !== $profile->profileId}
                <div class="s_photo">
                    {{ $avatarDesign->get($profile->username, $profile->firstName, $profile->sex, 64, $bRollover = true) }}
                </div>
            {/if}
        {/each}
    {/if}

    {if empty($related_profiles)}
        <p>{lang 'No related profiles found.'}</p>
    {/if}
</div>
