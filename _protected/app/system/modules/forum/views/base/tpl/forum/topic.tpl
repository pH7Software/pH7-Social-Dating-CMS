<div class="center">
    {if empty($error)}
        {each $topic in $topics}
            {{ $total_views = Framework\Mvc\Model\Statistic::getView($topic->topicId,DbTableName::FORUM_TOPIC) }}
            {{ $total_reply = (new ForumModel)->totalMessages($topic->topicId) }}

            <h3>
                <a href="{{ $design->url('forum', 'forum', 'post', "$topic->name,$topic->forumId,$topic->title,$topic->topicId") }}">
                    {% escape(Framework\Security\Ban\Ban::filterWord($topic->title), true) %}
                </a>
            </h3>

            <p>{% substr(escape(Framework\Security\Ban\Ban::filterWord($topic->message), true), 0, 100) %} | <span class="small italic">{lang 'Reply: %0%', $total_reply} | {lang 'Views: %0%', $total_views}</span></p>
        {/each}
    {else}
        <p>{error}</p>
    {/if}

    <p>
        {if isset($forum_name,$forum_id)}
            <a class="btn btn-default btn-sm" rel="nofollow" href="{{ $design->url('forum', 'forum', 'addtopic', "$forum_name,$forum_id") }}">{lang 'Create a new Topic'}</a>
        {else}
            <a class="btn btn-default btn-sm" rel="nofollow" href="{{ $design->url('forum', 'forum', 'search') }}">{lang 'New Search'}</a>
        {/if}
    </p>

    {main_include 'page_nav.inc.tpl'}

    <p>
        <a href="{{ $design->url('xml','rss','xmlrouter','forum-topic') }}">
            <img src="{url_static_img}icon/feed.svg" alt="RSS Feed" />
        </a>
    </p>
</div>
