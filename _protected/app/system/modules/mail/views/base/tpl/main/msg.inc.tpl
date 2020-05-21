{if empty($msg)}
  <p class="center bold">
    {lang 'That message was not found.'}
  </p>
{else}
  {* Set Variables *}
  {{ $username_sender = (empty($msg->username)) ? PH7_ADMIN_USERNAME : escape($msg->username) }}
  {{ $firstName_sender = (empty($msg->firstName)) ? PH7_ADMIN_USERNAME : escape($msg->firstName) }}
  {{ $subject = escape(Framework\Security\Ban\Ban::filterWord($msg->title)) }}
  {{ $message = Framework\Parse\Emoticon::init(Framework\Security\Ban\Ban::filterWord($msg->message)) }}
  {{ $is_outbox = ($msg->sender == $member_id) }}
  {{ $is_trash = (($msg->sender == $member_id && $msg->trash == 'sender') || ($msg->recipient == $member_id && $msg->trash == 'recipient') && !$is_outbox) }}
  {{ $is_delete = ($is_outbox || $is_trash) }}
  {{ $set_to = ($is_delete) ? 'setdelete' : 'settrash' }}
  {{ $label_txt = ($is_delete) ? t('Delete') : t('Trash') }}

  <div class="center">
    <dl>
      <dt>{lang 'Author:'}</dt>
      <dd>{{ $avatarDesign->get($username_sender, $firstName_sender, null, 32) }}</dd>
      <dt>{lang 'Subject:'}</dt>
      <dd>{subject}</dd>
      <dt>{lang 'Message:'}</dt>
      <dd>{message}</dd>
      <dt>{lang 'When:'}</dt>
      <dd>{% Framework\Date\Various::textTimeStamp($msg->sendDate) %}</dd>
    </dl>

    <div><a href="{{ $design->url('mail','main','compose',"$username_sender,$subject") }}">{lang 'Reply'}</a> | {{ LinkCoreForm::display($label_txt, 'mail', 'main', $set_to, array('id'=>$msg->messageId)) }}
    {if $is_trash} | {{ LinkCoreForm::display(t('Move to Inbox'), 'mail', 'main', 'setrestore', array('id'=>$msg->messageId)) }}{/if}</div>
  </div>
{/if}
