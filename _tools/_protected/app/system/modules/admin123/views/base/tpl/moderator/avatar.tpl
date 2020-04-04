{if !empty($avatars)}
    <ul>
        {each $avatar in $avatars}
            {{ $action = ($avatar->approvedAvatar == 1) ? 'disapprovedavatar' : 'approvedavatar' }}

            <div class="thumb_photo">
                {{ $avatarDesign->lightBox($avatar->username, $avatar->firstName, $avatar->sex, 300) }}
                <p class="italic">{lang 'Posted by'} {{ $design->getProfileLink($avatar->username) }}</p>

                <div>
                    {{ $text = ($avatar->approvedAvatar == 1) ? t('Disapproved') : t('Approved') }}
                    {{ LinkCoreForm::display($text, PH7_ADMIN_MOD, 'moderator', $action, array('id'=>$avatar->profileId, 'username'=>$avatar->username)) }} |
                    {{ LinkCoreForm::display(t('Delete'), PH7_ADMIN_MOD, 'moderator', 'deleteavatar', array('id'=>$avatar->profileId, 'username'=>$avatar->username)) }}
                </div>
            </div>
        {/each}
    </ul>
    {main_include 'page_nav.inc.tpl'}
{else}
    <p class="center">
        {lang 'No Profile Photos found for the moderation treatment.'}
    </p>
{/if}
