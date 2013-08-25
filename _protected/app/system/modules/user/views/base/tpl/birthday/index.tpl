<div class="center">

{if !empty($births)}

  <p>{total_births}</p>

  {each $birth in $births}

    <div class="s_photo">
      {{ $avatarDesign->get($birth->username, $birth->firstName, $birth->sex, 64, true) }}
    </div>

  {/each}

  {main_include 'page_nav.inc.tpl'}

{else}

  {{ $content = Framework\Parse\Emoticon::init(t('No users have a birthday today. Come back tomorrow ;-)')) }}
  <p>{content}</p>

{/if}

</div>
