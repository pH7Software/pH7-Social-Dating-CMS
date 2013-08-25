<div class="center">

{if sizeof($urls) > 0}
  <ul>
    {each $key => $value in $urls}
      <li class="underline"><a href="{key}">{value}</a></li>
    {/each}
  </ul>
{else}
  <p>{lang 'No links found at %site_name%'}</p>
{/if}

</div>
