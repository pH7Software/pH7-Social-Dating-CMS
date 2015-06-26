<div class="center">

{if empty($error)}

  {each $category in $categories}
    <h2 class="s_tMarg">{% $category->title %}</h2>
    {if AdminCore::auth()}
      <a class="s_button" href="{{ $design->url('forum', 'admin', 'editcategory', $category->categoryId) }}">{lang 'Edit Category'}</a> | {{ $design->popupLinkConfirm(t('Delete Category'), 'forum', 'admin', 'deletecategory', $category->categoryId, 's_button') }}<br /><br />
    {/if}

    {each $forum in $forums}

      {if $category->categoryId == $forum->categoryId}

        <h4><a href="{{ $design->url('forum', 'forum', 'topic', "$forum->name,$forum->forumId") }}">{% escape($forum->name, true) %}</a></h4>
        <p>{% escape($forum->description, true) %}</p>

        {if AdminCore::auth()}
          <a class="s_button" href="{{ $design->url('forum', 'admin', 'editforum', $forum->forumId) }}">{lang 'Edit Forum'}</a> | {{ $design->popupLinkConfirm(t('Delete Forum'), 'forum', 'admin', 'deleteforum', $forum->forumId, 's_button') }}<br />
        {/if}

      {/if}

    {/each}

  {/each}

  {main_include 'page_nav.inc.tpl'}

{else}

  <p>{error}</p>

{/if}

{if AdminCore::auth()}
  <br /><hr /><p><a class="m_button" href="{{ $design->url('forum', 'admin', 'addcategory') }}">{lang 'Add Category'}</a> | <a class="m_button" href="{{ $design->url('forum', 'admin', 'addforum') }}">{lang 'Add Forum'}</a></p>
{/if}

</div>
