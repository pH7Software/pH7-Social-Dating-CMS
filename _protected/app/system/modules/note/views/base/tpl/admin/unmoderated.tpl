{if !empty($posts)}
    {include 'home.inc.tpl'}
{else}
      <p class="center">
          {lang 'No Notes found for the moderation treatment.'}
      </p>
{/if}