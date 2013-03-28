<ol id="toc">
    <li><a href="#general"><span>{@lang('General Information')@}</span></a></li>
    <li><a href="#edit"><span>{@lang('Edit')@}</span></a></li>
    <li><a href="#design"><span>{@lang('Design')@}</span></a></li>
    <li><a href="#notification"><span>{@lang('Notifications')@}</span></a></li>
    <li><a href="#privacy"><span>{@lang('Privacy')@}</span></a></li>
    <li><a href="#passwrd"><span>{@lang('Change Password')@}</span></a></li>
</ol>

<div class="content" id="general">
 <p>{@lang('Your settings of your account %site_name%.')@}</p>
</div>

<div class="content" id="edit">
  {@manual_include('edit.tpl')@}
</div>

<div class="content" id="design">
  {@manual_include('design.tpl')@}
</div>

<div class="content" id="notification">
  {@manual_include('notification.tpl')@}
</div>

<div class="content" id="privacy">
  {@manual_include('privacy.tpl')@}
</div>

<div class="content" id="passwrd">
  {@manual_include('password.tpl')@}
</div>

<script src="{url_static}js/tabs.js"></script>
<script>tabs('p', ['general','edit','design','notification','privacy','passwrd']);</script>
