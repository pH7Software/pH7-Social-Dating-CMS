<div class="box-left col-md-3 col-lg-3 col-xl-2">
    <div class="design-box">
        <h2>{lang 'Search Blog Posts'}</h2>
        {{ SearchBlogForm::display(PH7_WIDTH_SEARCH_FORM) }}
    </div>

    <div class="design-box">
        <h2>{lang 'Categories'}</h2>
        <ul>
            {each $category in $categories}
                <li>
                    <a href="{{ $design->url('blog','main','category',$category->name) }}" title="{% $category->name %}" data-load="ajax">{% $category->name %}</a> - ({% $category->totalBlogs %})
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Popular Posts'}</h2>
        <ul>
            {each $views in $top_rating}
                <li>
                    <a href="{{ $design->url('blog','main','read',$views->postId) }}" title="{% $views->pageTitle %}" data-load="ajax">{% $views->title %}</a>
                </li>
            {/each}
        </ul>
    </div>

    <div class="design-box">
        <h2>{lang 'Top Rated Posts'}</h2>
        <ul>
            {each $rating in $top_rating}
                <li>
                    <a href="{{ $design->url('blog','main','read',$rating->postId) }}" title="{% $rating->pageTitle %}" data-load="ajax">{% $rating->title %}</a>
                </li>
            {/each}
        </ul>
    </div>
</div>

<div class="box-right col-md-9 col-lg-9 col-xl-9 col-xl-offset-1">
    <div class="center">
        {if !empty($error)}
            <p>{error}</p>
        {else}
            {each $post in $posts}
                <h1>
                    <a href="{{ $design->url('blog','main','read',$post->postId) }}" title="{% $post->title %}" data-load="ajax">{% escape($post->title) %}</a>
                </h1>

                <div class="left">
                    <a href="{{ $design->url('blog','main','read',$post->postId) }}" class="pic thumb" data-load="ajax">
                        <img src="{% Blog::getThumb($post->blogId) %}" alt="{% $post->pageTitle %}" title="{% $post->pageTitle %}" />
                    </a>
                </div>

                {* Don't check the post with \PH7\Framework\Security\Ban\Ban::filterWord() since this blog is only allowed for administrators *}
                {% escape($str->extract($post->content, 400), true) %}
                <p><a href="{{ $design->url('blog','main','read',$post->postId) }}" data-load="ajax">{lang 'See more'}</a></p>

                {if AdminCore::auth()}
                    <p><a class="btn btn-default btn-sm" href="{{ $design->url('blog', 'admin', 'edit', $post->blogId) }}">{lang 'Edit Article'}</a> | {{ $design->popupLinkConfirm(t('Delete Article'), 'blog', 'admin', 'delete', $post->blogId, 'btn btn-default btn-sm') }}</p>
                {/if}
                {{ $design->socialMediaWidgets() }}
                <hr /><br />
            {/each}
            {main_include 'page_nav.inc.tpl'}
        {/if}
        <br />

        <p>
            {if AdminCore::auth()}
                <a class="btn btn-default btn-sm" href="{{ $design->url('blog', 'admin', 'add') }}">
                    {lang 'Add a new Article'}
                </a>
            {/if}

            <a class="btn btn-default btn-sm" href="{{ $design->url('blog','main','search') }}">
                {lang 'Search for Blog Post'}
            </a>
        </p>
        <p>
            <a href="{{ $design->url('xml','rss','xmlrouter','blog') }}">
                <img src="{url_static_img}icon/feed.svg" alt="RSS Feed" />
            </a>
        </p>
    </div>
</div>
