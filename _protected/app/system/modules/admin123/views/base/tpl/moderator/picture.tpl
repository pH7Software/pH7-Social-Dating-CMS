{if !empty($pictures)}
    <ul>
        {each $picture in $pictures}
            {{ $action = ($picture->approved == 1) ? 'disapprovedphoto' : 'approvedphoto' }}

            <div class="thumb_photo">
                <a href="{url_data_sys_mod}picture/img/{% $picture->username %}/{% $picture->albumId %}/{%  $file = str_replace('original', '1000',  $picture->file) %}" title="{% $picture->title %}" data-popup="image">
                    <img src="{url_data_sys_mod}picture/img/{% $picture->username %}/{% $picture->albumId %}/{%  $file = str_replace('original', '400',  $picture->file) %}" alt="{% $picture->title %}" title="{% $picture->title %}" />
                </a>
                <p class="italic">
                    {lang 'Posted by'} {{ $design->getProfileLink($picture->username) }}<br />
                    <small>{lang 'Posted on %0%', $picture->createdDate}</small>
                </p>

                <div>
                    {{ $text = ($picture->approved == 1) ? t('Disapproved') : t('Approved') }}
                    {{ LinkCoreForm::display($text, PH7_ADMIN_MOD,'moderator', $action, array('picture_id'=>$picture->pictureId, 'id'=>$picture->profileId)) }} |
                    {{ LinkCoreForm::display(t('Delete'), PH7_ADMIN_MOD, 'moderator', 'deletephoto', array('album_id'=>$picture->albumId, 'picture_id'=>$picture->pictureId, 'id'=>$picture->profileId, 'username'=>$picture->username, 'picture_link'=>$picture->file)) }}
                </div>
            </div>
        {/each}
    </ul>
    {main_include 'page_nav.inc.tpl'}
{else}
    <p class="center">
        {lang 'No Pictures found for the moderation treatment.'}
    </p>
{/if}
