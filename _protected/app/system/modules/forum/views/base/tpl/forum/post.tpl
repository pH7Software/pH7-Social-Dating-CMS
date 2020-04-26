<div class="center">
    {if empty($error)}
        {if empty($post->username)}
            {{ $post->username = PH7_GHOST_USERNAME }}
        {/if}

        {* Main Post *}
        <div class="left">
            {{ $avatarDesign->get($post->username, $post->firstName, $post->sex, 64) }}<br />
            <p>
                <a
                    href="{{ $design->url('forum','forum','showpostbyprofile', $post->username) }}"
                    title="{lang "See %0%'s topics", $post->username}"
                    data-load="ajax">{lang "%0%'s topics", $post->username}
                </a>
            </p>
        </div>

        <p>
            {% Framework\Parse\Emoticon::init(
                Framework\Security\Ban\Ban::filterWord($post->message)
            ) %}
        </p>

        <div class="post-ident">
            {{ $design->littleSocialMediaWidgets() }}
            <p class="small italic">
                {lang 'Posted on %0%', $dateTime->get($post->createdDate)->dateTime()}
                {if !empty($post->updatedDate)}
                    | <span class="post-edit">{lang 'Last Edited %0%', $dateTime->get($post->updatedDate)->dateTime()}</span>
                {/if}
            </p>
            <p>
                {{ $design->like($post->username, $post->firstName, $post->sex) }} | {{ $design->report($post->profileId, $post->username, $post->firstName, $post->sex) }}
            </p>
        </div>

        {if !empty($post->profileId) AND $is_admin_auth AND !UserCore::isAdminLoggedAs()}
            <p class="underline">
                <a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$post->profileId) }}" title="{lang 'Login As this User to edit their topic'}">{lang 'Login as this User'}</a>
            </p>
        {/if}

        <a class="btn btn-default btn-sm" rel="nofollow" href="{{ $design->url('forum', 'forum', 'reply', "$post->name,$post->forumId,$post->title,$post->topicId") }}" title="{lang 'Reply to the topic'}">{lang 'Reply'}</a>

        {if $is_user_auth AND $member_id == $post->profileId}
            | <a class="btn btn-default btn-sm" href="{{ $design->url('forum', 'forum', 'edittopic', "$post->name,$post->forumId,$post->title,$post->topicId") }}" title="{lang 'Edit your topic'}">{lang 'Edit'}</a> |  {{ $design->popupLinkConfirm(t('Delete Topic'), 'forum', 'forum', 'deletetopic', $post->topicId.'_'.$post->forumId.'_'.$post->name, 'btn btn-default btn-sm') }}
        {/if}

        {* Replies *}
        {if !empty($messages)}
            {each $msg in $messages}
                {{ $relative_url = Framework\Mvc\Router\Uri::get('forum', 'forum', 'post', "$post->name,$post->forumId,$post->title,$post->topicId") . '#' . $msg->messageId }}

                <div class="msg_content" id="{% $msg->messageId %}">
                    <div class="left">
                        {{ $avatarDesign->get($msg->username, $msg->firstName, $msg->sex, 64) }}<br />
                        <p>
                            <a
                                href="{{ $design->url('forum','forum','showpostbyprofile', $msg->username) }}"
                                title="{lang "See %0%'s topics", $msg->username}"
                                data-load="ajax">{lang "%0%'s topics", $msg->username}
                            </a>
                        </p>
                    </div>

                    <p>
                        {% Framework\Parse\Emoticon::init(
                            Framework\Parse\User::atUsernameToLink(
                                Framework\Security\Ban\Ban::filterWord($msg->message)
                            )
                        ) %}
                    </p>

                    <div class="post-ident">
                        <p class="small italic">
                            <a href="{relative_url}">#</a> | {lang 'Posted on %0%', $dateTime->get($msg->createdDate)->dateTime()}
                            {if !empty($msg->updatedDate)} | <span class="post-edit">{lang 'Last Edited %0%', $dateTime->get($msg->updatedDate)->dateTime()}</span>{/if}
                        </p>
                        <p>{{ $design->like($msg->username, $msg->firstName, $msg->sex) }} | {{ $design->report($msg->profileId, $msg->username, $msg->firstName, $msg->sex) }}</p>
                    </div>

                    {if $is_user_auth AND $member_id == $msg->profileId}
                        <a class="btn btn-default btn-sm" href="{{ $design->url('forum', 'forum', 'editmessage', "$post->name,$post->forumId,$post->title,$msg->topicId,$msg->messageId") }}" title="{lang 'Edit your post'}">{lang 'Edit'}</a> | {{ $design->popupLinkConfirm(t('Delete Post'), 'forum', 'forum', 'deletemessage', $msg->messageId.'_'.$msg->topicId.'_'.$post->forumId.'_'.$post->title.'_'.$post->name, 'btn btn-default btn-sm') }}
                    {/if}

                    {if !empty($msg->profileId) AND $is_admin_auth AND !UserCore::isAdminLoggedAs()}
                        <p class="underline"><a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$msg->profileId) }}" title="{lang 'Login As this User to edit their post'}">{lang 'Login as this User'}</a></p>
                    {/if}
                </div>
            {/each}
            <p>
                <a class="btn btn-default btn-sm" rel="nofollow" href="{{ $design->url('forum', 'forum', 'reply', "$post->name,$post->forumId,$post->title,$post->topicId") }}" title="{lang 'Reply to the topic'}">{lang 'Reply'}</a>
            </p>
        {/if}

        {if !empty($messages)}
            {main_include 'page_nav.inc.tpl'}
        {/if}
        <p>
            <a href="{{ $design->url('xml','rss','xmlrouter','forum-post,'.$post->topicId) }}">
                <img src="{url_static_img}icon/feed.svg" alt="RSS Feed" />
            </a>
        </p>
    {else}
        <p>{error}</p>
    {/if}
</div>
