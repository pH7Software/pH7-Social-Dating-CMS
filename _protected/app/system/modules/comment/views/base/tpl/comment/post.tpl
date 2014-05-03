<div class="center">

{if empty($error)}

{{ $avatarDesign->get($com->username, $com->firstName, $com->sex, 32) }}
{{ $comment = nl2br(Framework\Parse\User::atUsernameToLink(Framework\Parse\Emoticon::init(escape(Framework\Security\Ban\Ban::filterWord($com->comment), true)))) }}

<p class="com_txt center">{comment}</p>
<br /><hr />

<div class="post-ident">
  <p class="small italic">{lang 'Posted on'} {% Framework\Date\Various::textTimeStamp($com->createdDate) %}
  {if !empty($com->updatedDate)} | <span class="post-edit">{lang 'Last Edited'} {% Framework\Date\Various::textTimeStamp($com->updatedDate) %}</span>{/if}</p>
  <p class="center">{{ $design->like($com->username,$com->firstName,$com->sex) }} | {{ $design->report($com->sender,$com->username,$com->firstName,$com->sex) }}</p>
</div>

{if (UserCore::auth() && ($member_id == $com->sender || $member_id == $com->recipient)) || AdminCore::auth()}
 <div><a href="{{ $design->url('comment','comment','edit',"$table,$com->recipient,$com->sender,$com->commentId") }}">{lang 'Edit'}</a> |
 {{ LinkCoreForm::display(t('Delete'), 'comment', 'comment', 'delete', array('table'=>$table, 'recipient_id'=>$com->recipient, 'sender_id'=>$com->sender, 'id'=>$com->commentId)) }}</div>
{/if}

<br />
<p class="bold italic"><a href="{{ $design->url('comment','comment','add',"$table,$com->recipient") }}">{lang 'Add a comment'}</a></p>

{else}
<p>{error}</p>
{/if}

</div>
