<div class="center">

  {if empty($error)}

    {each $album in $albums}

      <div class="thumb_photo">
        <a href="{{ $design->url('picture', 'main', 'albums', $album->username) }}"><img src="{url_data_sys_mod}picture/img/{% $album->username %}/{% $album->albumId %}/{% $album->thumb %}" alt="{% $album->name %}" title="{% $album->name %}" /></a>
        <p>{lang 'by'} <a href="{% $oUser->getProfileLink($album->username) %}">{% $album->firstName %}</a> {lang 'in'} <a href="{{ $design->url('picture', 'main', 'album', "$album->username,$album->name,$album->albumId") }}" title="{lang 'Album created on %0%.', $album->createdDate} {if !empty($album->updatedDate)} <br>{lang 'Modified on %0%.', $album->updatedDate} {/if}">{% $album->name %}</a></p>
      </div>

    {/each}

    {main_include 'page_nav.inc.tpl'}

  {else}

    <p>{error}</p>

  {/if}

  <p class="bottom"><a class="m_button" href="{{ $design->url('picture', 'main', 'addalbum') }}">{lang 'Add a new album'}</a></p>

</div>
