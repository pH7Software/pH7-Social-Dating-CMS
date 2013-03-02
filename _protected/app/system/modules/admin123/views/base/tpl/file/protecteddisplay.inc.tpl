<div class="center">

{@if(!empty($filesList))@}

<ul>
  {@foreach($filesList as $file)@}
    <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'file', 'protectededit', str_replace(PH7_PATH_PROTECTED, '', $file), false) }}" title="{@lang('Click for display/edit this file')@}">{% $file %}</a></li>
  {@/foreach@}
</ul>

{@else@}

  <p>{@lang('Not Found Files.')@}</p>

{@/if@}

</div>
