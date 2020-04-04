<div class="center">
    {if empty($error)}
        {each $category in $categories}
            <h2 class="s_tMarg underline">{% $category->title %}</h2>
            {if AdminCore::auth()}
                <a class="btn btn-default btn-sm" href="{{ $design->url('forum', 'admin', 'editcategory', $category->categoryId) }}">{lang 'Edit'}</a> | {{ $design->popupLinkConfirm(t('Delete'), 'forum', 'admin', 'deletecategory', $category->categoryId, 'btn btn-default btn-sm') }}<br /><br />
            {/if}

            {each $forum in $forums}
                {if $category->categoryId == $forum->categoryId}
                    <h3 class="italic"><a href="{{ $design->url('forum', 'forum', 'topic', "$forum->name,$forum->forumId") }}">{% escape($forum->name, true) %}</a></h3>
                    <p>{% escape($forum->description, true) %}</p>

                    {if AdminCore::auth()}
                        <a class="btn btn-default btn-sm" href="{{ $design->url('forum', 'admin', 'editforum', $forum->forumId) }}">{lang 'Edit'}</a> | {{ $design->popupLinkConfirm(t('Delete'), 'forum', 'admin', 'deleteforum', $forum->forumId, 'btn btn-default btn-sm') }}<br /><br />
                    {/if}
                {/if}
            {/each}
        {/each}

        {main_include 'page_nav.inc.tpl'}
    {else}
        <p>{error}</p>
    {/if}

    {if AdminCore::auth()}
        <br /><hr />
        <p>
            <a class="btn btn-default btn-md" href="{{ $design->url('forum', 'admin', 'addcategory') }}">{lang 'Add Category'}</a> <a class="btn btn-default btn-md" href="{{ $design->url('forum', 'admin', 'addforum') }}">{lang 'Add Forum'}</a>
        </p>
    {/if}
</div>
