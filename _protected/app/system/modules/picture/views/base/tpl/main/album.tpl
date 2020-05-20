{if empty($error)}
    {each $a in $album}
        {* Set Picture Album Statistics *}
        {{ Framework\Analytics\Statistic::setView($a->albumId, DbTableName::ALBUM_PICTURE) }}
        <div class="m_photo center">
            {{ $absolute_url = Framework\Mvc\Router\Uri::get('picture','main','photo',"$a->username,$a->albumId,$a->title,$a->pictureId") }}

            <h4><a href="{absolute_url}">{% substr(Framework\Security\Ban\Ban::filterWord($a->title),0,25) %}</a></h4>
            <p>
                <a href="{url_data_sys_mod}picture/img/{% $a->username %}/{% $a->albumId %}/{% str_replace('original', 1000, $a->file) %}" title="{% $a->title %}" data-popup="slideshow">
                    <img src="{url_data_sys_mod}picture/img/{% $a->username %}/{% $a->albumId %}/{% str_replace('original', '400', $a->file) %}" alt="{% $a->title %}" title="{% $a->title %}" />
                </a>
            </p>

            {if $is_user_auth AND $member_id == $a->profileId}
                <div class="small">
                    <a href="{{ $design->url('picture', 'main', 'editphoto', "$a->albumId,$a->title,$a->pictureId") }}">{lang 'Edit'}</a> |
                    {{ LinkCoreForm::display(t('Delete'), 'picture', 'main', 'deletephoto', array('album_title'=>$a->name, 'album_id'=>$a->albumId, 'picture_id'=>$a->pictureId, 'picture_link'=>$a->file)) }}
                </div>
            {/if}
            <p>
                {{ RatingDesignCore::voting($a->pictureId,DbTableName::PICTURE, 'center vs_tbMarg') }}
                {{ $design->like($a->username,$a->firstName,$a->sex,$absolute_url) }} | {{ $design->report($a->profileId, $a->username, $a->firstName, $a->sex) }}
            </p>
        </div>
    {/each}
    {main_include 'page_nav.inc.tpl'}

    {if $is_user_auth AND $member_id == $a->profileId}
        <p class="center s_tMarg bottom">
            <a class="btn btn-default btn-md" href="{{ $design->url('picture', 'main', 'addphoto', $a->albumId) }}">
                {lang 'Add new pictures'}
            </a>
        </p>
    {/if}
{else}
  <p>{error}</p>
{/if}
