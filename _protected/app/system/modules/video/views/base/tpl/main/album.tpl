{if empty($error)}
    {each $a in $album}
        {* Set Video Album Statistics *}
        {{ Framework\Analytics\Statistic::setView($a->albumId, DbTableName::ALBUM_VIDEO) }}

        <div class="m_photo center">
            {{ $absolute_url = Framework\Mvc\Router\Uri::get('video','main','video',"$a->username,$a->albumId,$a->title,$a->videoId") }}
            <h4><a href="{absolute_url}">{% substr(Framework\Security\Ban\Ban::filterWord($a->title),0,25) %}</a></h4>
            {{ VideoDesign::generate($a, VideoDesign::PREVIEW_MEDIA_MODE, '100%', 400) }}

            {if $is_user_auth AND $member_id == $a->profileId}
                <div class="small">
                    <a href="{{ $design->url('video', 'main', 'editvideo', "$a->albumId,$a->title,$a->videoId") }}">{lang 'Edit'}</a> |
                    {{ LinkCoreForm::display(t('Delete'), 'video', 'main', 'deletevideo', array('album_title'=>$a->name,'album_id'=>$a->albumId,'video_id'=>$a->videoId,'video_link'=>$a->file)) }}
                </div>
            {/if}
            <p>
                {{ RatingDesignCore::voting($a->videoId,DbTableName::VIDEO, 'center vs_tbMarg') }}
                {{ $design->like($a->username,$a->firstName,$a->sex,$absolute_url) }} | {{ $design->report($a->profileId, $a->username, $a->firstName, $a->sex) }}
            </p>
        </div>
    {/each}
    {main_include 'page_nav.inc.tpl'}

    {if $is_user_auth AND $member_id == $a->profileId}
        <p class="center s_tMarg bottom">
            <a class="btn btn-default btn-md" href="{{ $design->url('video', 'main', 'addvideo', $a->albumId) }}">
                {lang 'Add new videos'}
            </a>
        </p>
    {/if}
{else}
    <p>{error}</p>
{/if}
