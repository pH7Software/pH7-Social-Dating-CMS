<div class="center">
    {if empty($error)}
        <h2>{% Framework\Security\Ban\Ban::filterWord($picture->title) %}</h2>
        <div class="picture_block">
            <a href="{url_data_sys_mod}picture/img/{% $picture->username %}/{% $picture->albumId %}/{% str_replace('original', 1200, $picture->file) %}" title="{% $picture->title %}" data-popup="image">
                <img src="{url_data_sys_mod}picture/img/{% $picture->username %}/{% $picture->albumId %}/{% str_replace('original', '600', $picture->file) %}" alt="{% $picture->title %}" title="{% $picture->title %}" class="thumb" />
            </a>
        </div>

        <p>
            {% nl2br(Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($picture->description))) %}
        </p>
        <p class="italic">
            {lang 'Album created %0%', Framework\Date\Various::textTimeStamp($picture->createdDate)}
            {if !empty($picture->updatedDate)}
                <br />{lang 'Modified %0%', Framework\Date\Various::textTimeStamp($picture->updatedDate)}
            {/if}
        </p>
        <p class="italic">
            {lang 'Views:'} {% Framework\Mvc\Model\Statistic::getView($picture->pictureId,DbTableName::PICTURE) %}
        </p>

        {if $is_user_auth AND $member_id == $picture->profileId}
            <div class="small">
                <a href="{{ $design->url('picture', 'main', 'editphoto', "$picture->albumId,$picture->title,$picture->pictureId") }}">{lang 'Edit'}</a> |
                {{ LinkCoreForm::display(t('Delete'), 'picture', 'main', 'deletephoto', array('album_title'=>$picture->name, 'album_id'=>$picture->albumId, 'picture_id'=>$picture->pictureId, 'picture_link'=>$picture->file)) }}
            </div>
        {/if}

        {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('picture','main','photo',"$picture->username,$picture->albumId,$picture->title,$picture->pictureId")) }}
        {{ RatingDesignCore::voting($picture->pictureId,DbTableName::PICTURE,'center') }}
        {{ CommentDesignCore::link($picture->pictureId, 'picture') }}

        <p class="center">
            {{ $design->like($picture->username, $picture->firstName, $picture->sex) }} | {{ $design->report($picture->profileId, $picture->username, $picture->firstName, $picture->sex) }}
        </p>
        {{ $design->socialMediaWidgets() }}
    {else}
        <p>{error}</p>
    {/if}
</div>
