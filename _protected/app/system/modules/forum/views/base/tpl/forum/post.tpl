<div class="center">

{if empty($error)}

  {* Post *}

  <div class="left">{{ $avatarDesign->get($post->username, $post->firstName, $post->sex, 64) }}<br />
    <p><a href="{{ $design->url('forum','forum','showpostbyprofile',$post->username) }}" data-load="ajax">{lang 'Show Post of'} {% $post->username %}</a></p>
  </div>

  <p>{% Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($post->message)) %}</p>

  <div class="post-ident">
    <p class="small italic">{lang 'Posted on'} {% $dateTime->get($post->createdDate)->dateTime() %}
    {if !empty($post->updatedDate)} | <span class="post-edit">{lang 'Last Edited'} {% $dateTime->get($post->updatedDate)->dateTime() %}</span>{/if}</p>

    <p>{{ $design->like($post->username, $post->firstName, $post->sex) }} | {{ $design->report($post->profileId, $post->username, $post->firstName, $post->sex) }}</p>
  </div>

  {if AdminCore::auth() && !(new Framework\Session\Session)->exists('login_user_as')}
    <p><a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$post->profileId) }}" title="{lang 'Login As this User to edit his post'}">{lang 'Login as this User'}</a></p>
  {/if}

  <a class="m_button" rel="nofollow" href="{{ $design->url('forum', 'forum', 'reply', "$post->name,$post->forumId,$post->title,$post->topicId") }}" title="{lang 'Reply on the Message'}">{lang 'Reply'}</a>

  {if UserCore::auth() && $member_id == $post->profileId}
    | <a class="m_button" href="{{ $design->url('forum', 'forum', 'edittopic', "$post->name,$post->forumId,$post->title,$post->topicId") }}">{lang 'Edit your post'}</a> |  {{ $design->popupLinkConfirm(t('Delete your topic'), 'forum', 'forum', 'deletetopic', $post->topicId.'_'.$post->forumId.'_'.$post->name, 'm_button') }}
  {/if}

  {* Reply *}

  {if !empty($messages)}

    {each $msg in $messages}

      {{ $relative_url = Framework\Mvc\Router\Uri::get('forum', 'forum', 'post', "$post->name,$post->forumId,$post->title,$post->topicId") . '#' . $msg->messageId }}

      <div class="msg_content" id="{% $msg->messageId %}">

        <div class="left">{{ $avatarDesign->get($msg->username, $msg->firstName, $msg->sex, 64) }}<br />
          <p><a href="{{ $design->url('forum','forum','showpostbyprofile',$msg->username) }}" data-load="ajax">{lang 'Show Post of'} {% $msg->username %}</a></p>
        </div>

        <p>{% Framework\Parse\User::atUsernameToLink(Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($msg->message))) %}</p>

        <div class="post-ident">
          <p class="small italic"><a href="{relative_url}">#</a> | {lang 'Posted on'} {% $dateTime->get($msg->createdDate)->dateTime() %}
          {if !empty($msg->updatedDate)} | <span class="post-edit">{lang 'Last Edited'} {% $dateTime->get($msg->updatedDate)->dateTime() %}</span>{/if}</p>

          <p>{{ $design->like($msg->username, $msg->firstName, $msg->sex) }} | {{ $design->report($msg->profileId, $msg->username, $msg->firstName, $msg->sex) }}</p>
        </div>

        {if UserCore::auth() && $member_id == $msg->profileId}
          <a class="m_button" href="{{ $design->url('forum', 'forum', 'editmessage', "$post->name,$post->forumId,$post->title,$msg->topicId,$msg->messageId") }}">{lang 'Edit your post'}</a> | {{ $design->popupLinkConfirm(t('Delete your message'), 'forum', 'forum', 'deletemessage', $msg->messageId.'_'.$msg->topicId.'_'.$post->forumId.'_'.$post->title.'_'.$post->name, 'm_button') }}
        {/if}

        {if AdminCore::auth()}
          <p><a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$msg->profileId) }}" title="{lang 'Login As this User to edit his post'}">{lang 'Login as this User'}</a></p>
        {/if}

      </div>

    {/each}

    <p><a class="m_button" rel="nofollow" href="{{ $design->url('forum', 'forum', 'reply', "$post->name,$post->forumId,$post->title,$post->topicId") }}" title="{lang 'Reply on the Message'}">{lang 'Reply'}</a></p>

  {/if}

  {if !empty($messages)}
    {main_include 'page_nav.inc.tpl'}
  {/if}

  <p><a href="{{ $design->url('xml','rss','xmlrouter','forum-post,'.$post->topicId) }}"><img src="{url_static_img}icon/feed.png" alt="RSS Feed" /></a></p>

{else}

  <p>{error}</p>

{/if}

</div>
