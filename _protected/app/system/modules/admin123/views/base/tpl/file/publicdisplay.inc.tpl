<div class="center">
  {if !empty($filesList)}
    <ul>
      {each $file in $filesList}
        {{ $short_path = str_replace([PH7_PATH_ROOT, '\\', '//'], ['', '/', '/'], $file) }}
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'file', 'publicedit', $short_path, false) }}" title="{lang 'Click to display/edit this file'}">{short_path}</a></li>
      {/each}
    </ul>
  {else}
    <p>{lang 'Templates File Not Found!'}</p>
  {/if}
</div>
