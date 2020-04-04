{if !empty($backgrounds)}
    <ul>
        {each $background in $backgrounds}
            {{ $action = ($background->approved == 1) ? 'disapprovedbackground' : 'approvedbackground' }}
            <div class="thumb_photo">
                <a href="{url_data_sys_mod}user/background/img/{% $background->username %}/{% $background->file %}" title="{lang 'Wallpaper of'} {% $background->username %}" data-popup="image">
                    <img src="{url_data_sys_mod}user/background/img/{% $background->username %}/{% $background->file %}" alt="{lang 'Wallpaper'}" title="{lang 'Wallpaper of'} {% $background->username %}" />
                </a>
                <p class="italic">
                    {lang 'Posted by'} {{ $design->getProfileLink($background->username) }}
                </p>
                <div>
                    {{ $text = ($background->approved == 1) ? t('Disapproved') : t('Approved') }}
                    {{ LinkCoreForm::display($text, PH7_ADMIN_MOD, 'moderator', $action, array('id'=>$background->profileId)) }} |
                    {{ LinkCoreForm::display(t('Delete'), PH7_ADMIN_MOD, 'moderator', 'deletebackground', array('id'=>$background->profileId, 'username'=>$background->username)) }}
                </div>
            </div>
        {/each}
    </ul>
    {main_include 'page_nav.inc.tpl'}
{else}
    <p class="center">
        {lang 'No Profile Backgrounds found for the moderation treatment.'}
    </p>
{/if}
