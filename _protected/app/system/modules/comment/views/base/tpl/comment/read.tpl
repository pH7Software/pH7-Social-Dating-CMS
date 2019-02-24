<div class="center">
    {if empty($error)}
        {each $com in $comment}
            <div id="{% $com->commentId %}">
                {{ $absolute_url = Framework\Mvc\Router\Uri::get('comment','comment','post',"$table,$com->commentId") }}
                {{ $relative_url = Framework\Mvc\Router\Uri::get('comment','comment','read',"$table,$com->recipient") . '#' . $com->commentId }}

                {{ $avatarDesign->get($com->username, $com->firstName, $com->sex, 32) }}
                {{ $comment = nl2br(
                    Framework\Parse\User::atUsernameToLink(
                        Framework\Parse\Emoticon::init(
                            escape($str->extract(Framework\Security\Ban\Ban::filterWord($com->comment)), true)
                        )
                    )
                ) }}

                <p><span class="com_txt">{comment}</span><br />
                <a href="{absolute_url}">{lang 'See more'}</a></p>

                <div class="center post-ident">
                    <p class="small italic"><a href="{relative_url}">#</a> | {lang 'Posted %0%', Framework\Date\Various::textTimeStamp($com->createdDate)}
                        {if !empty($com->updatedDate)}
                            | <span class="post-edit">{lang 'Last Edited %0%', Framework\Date\Various::textTimeStamp($com->updatedDate)}</span>
                        {/if}
                    </p>
                    <p class="center">
                        {{ $design->like($com->username,$com->firstName,$com->sex,$absolute_url) }} | {{ $design->report($com->sender,$com->username,$com->firstName,$com->sex) }}
                    </p>
                </div>

                {if $is_user_auth && ($member_id == $com->sender || $member_id == $com->recipient)}
                    <p>
                        <a class="s_bMarg button_medium" href="{{ $design->url('comment','comment','edit',"$table,$com->recipient,$com->sender,$com->commentId") }}">
                            {lang 'Edit'}
                        </a> |
                        <a class="button_medium" href="javascript:void(0)" onclick="comment('delete',{% $com->commentId %},{% $com->recipient %},{% $com->sender %},'{table}','{csrf_token}')">
                            {lang 'Delete'}
                        </a>
                    </p>
                {/if}
            </div>
        {/each}

        <p class="s_tMarg bold italic">
            <a href="{{ $design->url('comment','comment','add',"$table,$com->recipient") }}">{lang 'Add a comment'}</a> &nbsp;
            <a href="{{ $design->url('xml','rss','xmlrouter',"comment-$table,$com->recipient") }}">
                <img src="{url_static_img}icon/feed.svg" alt="{lang 'RSS Feed'}" />
            </a>
        </p>

        {main_include 'page_nav.inc.tpl'}
    {else}
        <p>{error}</p>
    {/if}
</div>
