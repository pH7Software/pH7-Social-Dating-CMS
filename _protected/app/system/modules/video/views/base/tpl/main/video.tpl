<div class="center">
    {if empty($error)}
        <h2 class="s_bMarg">{% Framework\Security\Ban\Ban::filterWord($video->title) %}</h2>
        {{ VideoDesign::generate($video, VideoDesign::MOVIE_MEDIA_MODE, '100%', 440) }}

        <p>
            {% nl2br(Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($video->description))) %}
        </p>
        <p class="italic">
            {lang 'Album created %0%', Framework\Date\Various::textTimeStamp($video->createdDate)}
            {if !empty($video->updatedDate)}
                <br />{lang 'Modified %0%', Framework\Date\Various::textTimeStamp($video->updatedDate)}
            {/if}
        </p>
        <p class="italic">
            {lang 'Views:'} {% Framework\Mvc\Model\Statistic::getView($video->videoId,DbTableName::VIDEO) %}
        </p>

        {if $is_user_auth AND $member_id == $video->profileId}
            <div class="small">
                <a href="{{ $design->url('video', 'main', 'editvideo', "$video->albumId,$video->title,$video->videoId") }}">{lang 'Edit'}</a> |
                {{ LinkCoreForm::display(t('Delete'), 'video', 'main', 'deletevideo', array('album_title'=>$video->name, 'album_id'=>$video->albumId, 'video_id'=>$video->videoId, 'video_link'=>$video->file)) }}
            </div>
        {/if}

        {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('video','main','video',"$video->username,$video->albumId,$video->title,$video->videoId")) }}
        {{ RatingDesignCore::voting($video->videoId,DbTableName::VIDEO,'center') }}
        {{ CommentDesignCore::link($video->videoId, 'video') }}

        <p class="center">
            {{ $design->like($video->username, $video->firstName, $video->sex) }} | {{ $design->report($video->profileId, $video->username, $video->firstName, $video->sex) }}
        </p>
        {{ $design->socialMediaWidgets() }}
    {else}
        <p>{error}</p>
    {/if}
</div>
