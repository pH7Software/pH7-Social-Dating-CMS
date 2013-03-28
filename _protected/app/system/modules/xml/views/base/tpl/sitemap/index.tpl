<div class="center">

{@if(sizeof($urls) > 0)@}
<ul>
  {@foreach($urls as $key=>$value)@}
    <li class="underline"><a href="{key}">{value}</a></li>
  {@/foreach@}
</ul>
{@else@}
  <p>{@lang('No links found at %site_name%')@}</p>
{@/if@}

</div>
