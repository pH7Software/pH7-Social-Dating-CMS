<div class="center">

  {each $membership in $memberships}

    {if $membership->enable == 1 AND $membership->price != 0}
      <div class="border">
        <p class="bold"><a href="{{ $design->url('payment', 'main', 'pay', $membership->groupId) }}" title="{lang 'Purchase this membership!'}">{% $membership->name %}</a></p>
        <p class="italic">{% $membership->description %}</p>
      </div>
    {/if}

  {/each}

</div>
