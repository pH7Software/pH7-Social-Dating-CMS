<div class="center">
  {if !empty($filesList)}
    <ul>
      {each $file in $filesList}
        {{ $short_path = str_replace([PH7_PATH_PROTECTED, '\\', '//'], ['', '/', '/'], $file) }}
        <li><a href="{{ $design->url(PH7_ADMIN_MOD, 'file', 'protectededit', $short_path, false) }}" title="{lang 'Click to display/edit the file'}">{short_path}</a></li>
      {/each}
    </ul>
  {else}
    <p>{lang 'File Not Found!'}</p>
  {/if}
</div>
