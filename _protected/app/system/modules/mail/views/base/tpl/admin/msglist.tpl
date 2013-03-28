{@if(empty($error))@}

<form method="post" action="{{ $design->url('mail','main','inbox') }}">
{{ $designSecurity->inputToken('mail_action') }}

<p><input type="checkbox" name="all_action" /></p>

<div class="mb_nav">
 <div class="user">{@lang('Author')@}</div> <div class="subject">{@lang('Subject')@}</div> <div class="message">{@lang('Message')@}</div> <div class="date">{@lang('Date')@}</div> <div class="action">{@lang('Action')@}</div>
</div>

<div class="divShow">
{@foreach($inbox as $msg)@}

{* Set Variables *}
{{ $usernameSender = (empty($msg->username)) ? 'admin' : $msg->username }}
{{ $firstNameSender = (empty($msg->firstName)) ? 'admin' : $msg->firstName }}
{{ $subject = escape(substr(Framework\Security\Ban\Ban::filterWord($msg->title, false),0,20), true) }}
{{ $message = escape(Framework\Security\Ban\Ban::filterWord($msg->message), true) }}

  <div class="msg_content" id="mail_{% $msg->messageId %}">
    <div class="left"><input type="checkbox" name="action[]" value="{% $msg->messageId %}" /></div>
    {@if($msg->status == 1)@}<img src="{url_tpl_img}icon/new.gif" alt="{@lang('New Message')@}" title="{@lang('Unread')@}" />{@/if@}
    <div class="user">{{ $avatarDesign->get($usernameSender, $firstNameSender, null, 32) }}</div>
    <div class="content" title="{@lang('See more')@}"><a href="#divShow_{% $msg->messageId %}">
      <div class="subject">{subject}</div>
      <div class="message">{% substr($message,0,50) %}</div>
    </a></div>
    <div class="date">{% $dateTime->get($msg->sendDate)->dateTime() %}</div>
    <div class="action"><a href="javascript:void(0)" onclick="mail('delete',{% $msg->messageId %},'{csrf_token}')">{@lang('Delete')@}</a></div>

    {* The hidden message *}
    <div class="hidden center" id="divShow_{% $msg->messageId %}">{message}</div>

  </div>

{@/foreach@}
</div>

<p><input type="checkbox" name="all_action" /> <button type="submit" name="delete" formaction="{{ $design->url('mail','admin','deleteall') }}">{@lang('Delete')@}</button></p>

</form>

{@main_include('page_nav.inc.tpl')@}

<script>
$('button[name=delete]').click(function() {
   return confirm('{@lang('Caution! This action will remove you messages! (Irreversible Action)')@}');
});
</script>

{@else@}

<p class="center bold">{error}</p>

{@/if@}
