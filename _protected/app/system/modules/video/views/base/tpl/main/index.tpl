<div class="center">
    {if empty($error)}
        {each $album in $albums}
            <div class="thumb_photo">
                <p>
                    <a href="{{ $design->url('video', 'main', 'albums', $album->username) }}">
                        <img src="{url_data_sys_mod}video/file/{% $album->username %}/{% $album->albumId %}/{% $album->thumb %}" alt="{% $album->name %}" title="{% $album->name %}" />
                    </a>
                </p>

                <p>
                    {lang 'by'} {{ $design->getProfileLink($album->username) }} {lang 'in'} <a href="{{ $design->url('video', 'main', 'album', "$album->username,$album->name,$album->albumId") }}" title="{lang 'Album created on %0%', $album->createdDate}{if !empty($album->updatedDate)}<br> {lang 'Modified on %0%', $album->updatedDate}{/if}">{% $album->name %}</a>
                </p>
            </div>
        {/each}
        {main_include 'page_nav.inc.tpl'}
    {else}
        <p>{error}</p>
    {/if}

    <p class="bottom s_tMarg">
        <a class="btn btn-default btn-md" href="{{ $design->url('video', 'main', 'addalbum') }}">
            {lang 'Add a new album'}
        </a>
    </p>
</div>
