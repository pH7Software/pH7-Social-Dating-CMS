<div class="msg-menu">
  <a href="{{ $design->url('mail','main','compose') }}">Compose</a> |
  <a href="{{ $design->url('mail','main','inbox') }}">Inbox({count_unread_mail}) </a> |
  <a href="{{ $design->url('mail','main','outbox') }}">Sent</a> |
  <a href="{{ $design->url('mail','main','trash') }}">Trash</a> |
  <a href="{{ $design->url('user','setting','index') }}#p=notification">Settings</a> 
</div>
<div class="col-md-8">
    {{ MailForm::display() }}
</div>

<div class="col-md-4 ad_336_280">
    {designModel.ad(336, 280)}
</div>
