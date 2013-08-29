<ol id="toc">
  <li><a href="#edit"><span>{lang 'Edit Account'}</span></a></li>
  <li><a href="#avatar"><span>{lang 'Avatar'}</span></a></li>
  <li><a href="#design"><span>{lang 'Wallpaper'}</span></a></li>
  <li><a href="#notification"><span>{lang 'Notifications'}</span></a></li>
  <li><a href="#privacy"><span>{lang 'Privacy'}</span></a></li>
  <li><a href="#pwd"><span>{lang 'Change Password'}</span></a></li>
</ol>

<div class="content" id="edit">
  {manual_include 'edit.tpl'}
</div>

<div class="content" id="avatar">
  {manual_include 'avatar.tpl'}
</div>

<div class="content" id="design">
  {manual_include 'design.tpl'}
</div>

<div class="content" id="notification">
  {manual_include 'notification.tpl'}
</div>

<div class="content" id="privacy">
  {manual_include 'privacy.tpl'}
</div>

<div class="content" id="pwd">
  {manual_include 'password.tpl'}
</div>

<script src="{url_static}js/tabs.js"></script>
<script>tabs('p', ['edit','avatar','design','notification','privacy','pwd']);</script>
