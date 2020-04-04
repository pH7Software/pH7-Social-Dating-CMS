<div class="msg-menu">
    <a href="{{ $design->url('mail','main','compose') }}">Compose</a> |
    <a href="{{ $design->url('mail','main','inbox') }}">Inbox({count_unread_mail}) </a> |
    <a href="{{ $design->url('mail','main','outbox') }}">Sent</a> |
    <a href="{{ $design->url('mail','main','trash') }}">Trash</a> |
    <a href="{{ $design->url('user','setting','index') }}#p=notification">Settings</a> |
</div>

{if empty($msg)}
  <p class="bold center">
    {lang 'That message was not found.'}
  </p>
{else}

  {* Set Variables *}
  {{ $usernameSender = (empty($msg->username)) ? PH7_ADMIN_USERNAME : escape($msg->username) }}
  {{ $firstNameSender = (empty($msg->firstName)) ? PH7_ADMIN_USERNAME : escape($msg->firstName) }}
  {{ $subject = escape(Framework\Security\Ban\Ban::filterWord($msg->title)) }}
  {{ $message = Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($msg->message)) }}
  {{ $is_outbox = ($msg->sender == $member_id) }}
  {{ $is_trash = (($msg->sender == $member_id && $msg->trash == 'sender') || ($msg->recipient == $member_id && $msg->trash == 'recipient') && !$is_outbox) }}
  {{ $is_delete = ($is_outbox || $is_trash) }}
  {{ $set_to = ($is_delete) ? 'setdelete' : 'settrash' }}
  {{ $label_txt = ($is_delete) ? t('Delete') : t('Trash') }}

  {{ $subject = escape(str_replace('re ', '', $msg->title), true) }}

  <div class="left">
  <dl>
    <dt class="msg-label">{lang 'From:'}</dt>
    <dd>{{ $avatarDesign->get($usernameSender, $firstNameSender, null, 32) }}</dd>
    <dd class="msg-from"><?php echo $usernameSender ?></dd>
    <dt class="msg-label">{lang 'Subject:'}</dt>
    <dd class="msg-cc">{subject}</dd>
  </dl>
    
  {if !empty($msgsOld)}
           
  <h6>Previous Messages:</h6>

  {each $msg in $msgsOld}
  
  {* Set Variables *}
  {{ $usernameSender = (empty($msg->username)) ? PH7_ADMIN_USERNAME : escape($msg->username) }}
  {{ $firstNameSender = (empty($msg->firstName)) ? PH7_ADMIN_USERNAME : escape($msg->firstName) }}
  {{ $subject = escape(Framework\Security\Ban\Ban::filterWord($msg->title)) }}
  {{ $message = Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($msg->message)) }}
  {{ $is_outbox = ($msg->sender == $member_id) }}
  {{ $is_trash = (($msg->sender == $member_id && $msg->trash == 'sender') || ($msg->recipient == $member_id && $msg->trash == 'recipient') && !$is_outbox) }}
  {{ $is_delete = ($is_outbox || $is_trash) }}
  {{ $set_to = 'setdelete' }}
  {{ $label_txt = t('Delete') }}
  
  {{ $subject = escape(str_replace('re ', '', $msg->title), true) }}
    
      <!-- Sent by User -->
      <div class="msg-left">
        <dl>
          <dd class="msg-text-area">{message}</dd>
           <div>{{ LinkCoreForm::display($label_txt, 'mail', 'main', $set_to, array('id'=>$msg->messageId)) }}
          {if $is_trash} | {{ LinkCoreForm::display(t('Move to Inbox'), 'mail', 'main', 'setrestore', array('id'=>$msg->messageId)) }}{/if}</div>
          {if $msg->sender === $member_id}
          <dt class="msg-l-sent">Sent: &nbsp;{% Framework\Date\Various::textTimeStamp($msg->sendDate) %}</dt>
          {else}
          <dt class="msg-l-received">Received: &nbsp;{% Framework\Date\Various::textTimeStamp($msg->sendDate) %}</dt>
          {/if}
        </dl>
        
    {/each}
    {/if}
        <hr>
       

        {{ MailForm::display() }}
        
       
      </div>
{/if}
