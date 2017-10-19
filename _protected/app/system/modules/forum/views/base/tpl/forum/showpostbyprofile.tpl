<div class="center" id="forum_block">
    {if empty($error)}
        <p class="italic underline s_bMarg">
            <strong><a href="{{ $design->url('forum','forum','showpostbyprofile',$username) }}">{topic_number}</a></strong>
        </p>
        {each $topic in $topics}
            <h3>
                <a href="{{ $design->url('forum', 'forum', 'post', "$topic->name,$topic->forumId,$topic->title,$topic->topicId") }}">
                    {% escape(Framework\Security\Ban\Ban::filterWord($topic->title), true) %}
                </a>
            </h3>

            <p>{% substr(escape(Framework\Security\Ban\Ban::filterWord($topic->message), true), 0, 100) %}</p>
        {/each}

        {main_include 'page_nav.inc.tpl'}
    {else}
        <p>{error}</p>
    {/if}
</div>
