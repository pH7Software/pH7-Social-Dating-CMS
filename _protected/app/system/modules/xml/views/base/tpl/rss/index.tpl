<div class="center">

{@if(sizeof($urls) > 0)@}
  <ul>
    {@foreach($urls as $key => $value)@}
      <li><img src="{url_static_img}icon/feed.png" alt="RSS Feed" />&nbsp;<span class="underline"><a href="{key}">{value}</a></span></li>
    {@/foreach@}
  </ul>
{@else@}
  <p>{@lang('No RSS Feed found at %site_name%')@}</p>
{@/if@}

</div>
