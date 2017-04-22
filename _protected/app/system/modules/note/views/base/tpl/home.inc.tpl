<div class="box-left">
    <div class="design-box">
        <h2>{lang 'Search Note Posts'}</h2>
        {{ SearchNoteForm::display(PH7_WIDTH_SEARCH_FORM) }}
    </div>

    <div class="design-box">
        <h2>{lang 'Top Authors'}</h2>
        <ul>
            {each $author in $authors}
                <li>
                    <a href="{{ $design->url('note','main','author',$author->username) }}" title="{% $author->username %}" data-load="ajax">{% substr($author->username,0,20) %}</a> - ({% $author->totalAuthors %})
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Categories'}</h2>
        <ul>
            {each $category in $categories}
                <li>
                    <a href="{{ $design->url('note','main','category',$category->name) }}" title="{% $category->name %}" data-load="ajax">{% $category->name %}</a> - ({% $category->totalCatNotes %})
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Popular Posts'}</h2>
        <ul>
            {each $views in $top_views}
              <li><a href="{{ $design->url('note','main','read',"$views->username,$views->postId") }}" title="{% $views->pageTitle %}" data-load="ajax">{% $views->title %}</a></li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Rated Posts'}</h2>
        <ul>
            {each $rating in $top_rating}
              <li><a href="{{ $design->url('note','main','read',"$rating->username,$rating->postId") }}" title="{% $rating->pageTitle %}" data-load="ajax">{% $rating->title %}</a></li>
            {/each}
        </ul>
    </div>
</div>

<div class="box-right">
    <div class="center" id="note_block">
        {if !empty($error)}
            <p>{error}</p>
        {else}
            {each $post in $posts}
                {{ $content = escape($this->str->extract(Framework\Security\Ban\Ban::filterWord($post->content),0,400), true) }}

                <h1><a href="{{ $design->url('note','main','read',"$post->username,$post->postId") }}" title="{% $post->title %}" data-load="ajax">{% escape(Framework\Security\Ban\Ban::filterWord($post->title)) %}</a></h1>

                <div class="left">{{ NoteDesign::thumb($post) }}</div>
                {content}
                <p><a href="{{ $design->url('note','main','read',"$post->username,$post->postId") }}" data-load="ajax">{lang 'See more'}</a></p>

                {if $is_user_auth AND $member_id === $post->profileId}
                    <p>
                        <a class="btn btn-default btn-sm" href="{{ $design->url('note','main','edit',$post->noteId) }}">{lang 'Edit Article'}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'note','main','delete', $post->noteId, 'btn btn-default btn-sm') }}
                    </p>
                {/if}

                {if $is_admin_auth AND !UserCore::isAdminLoggedAs()}
                    {{ $action = ($post->approved == 1) ? 'disapproved' : 'approved' }}
                    {{ $text = ($post->approved == 1) ? t('Disapprove') : t('Approve') }}
                    <fieldset class="s_tMarg">
                        <legend>{lang 'Moderation Action'}</legend>
                        <div>
                            {{ LinkCoreForm::display($text, 'note', 'admin', $action, array('note_id'=>$post->noteId, 'post_id'=>$post->postId, 'profile_id'=>$post->profileId)) }} &nbsp; | &nbsp; <a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$post->profileId) }}" title="{lang 'Login as this author to edit this post. Please first approve this note as an administrator to be able to edit or delete it.'}">{lang 'Login as this User'}</a>
                        </div>
                    </fieldset>
                {/if}

                {{ $design->likeApi() }}
                <hr /><br />
            {/each}
            {main_include 'page_nav.inc.tpl'}
        {/if}
    </div>

    <div class="center">
        <p>
            <a class="btn btn-default btn-sm" href="{{ $design->url('note','main','add') }}">{lang 'Add a new Article'}</a>
            <a class="btn btn-default btn-sm" href="{{ $design->url('note','main','search') }}">{lang 'Search a Note'}</a>
        </p>
        <p>
            <a href="{{ $design->url('xml','rss','xmlrouter','note') }}">
                <img src="{url_static_img}icon/feed.png" alt="RSS Feed" />
            </a>
        </p>
    </div>
</div>
