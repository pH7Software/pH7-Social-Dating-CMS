<table class="center">

  <tr>
    <th>{lang 'Group ID#'}</th>
    <th>{lang 'Name'}</th>
    <th>{lang 'Price (%0%)', $this->config->values['module.setting']['currency']}</th>
    <th>{lang 'Expiration Days'}</th>
    <th>{lang 'Active'}</th>
    <th>{lang 'Action'}</th>
  </tr>


  {each $membership in $memberships}

    <tr>
      <td>{% $membership->groupId %}</td>
      <td>{% $membership->name %}</td>
      <td>{% $membership->price %}</td>
      <td>{% $membership->expirationDays %}</td>
      <td>{if $membership->enable == 1} <span class="green1">{lang 'Enable'}</span> {else} <span class="red">{lang 'Disable'}</span> {/if}</td>
      <td><a href="{{ $design->url('payment', 'admin', 'editmembership', $membership->groupId) }}">{lang 'Edit'}</a> | {{ $design->popupLinkConfirm(t('Delete (Irreversible!)'), 'payment', 'admin', 'deletemembership', $membership->groupId) }}</td>
    </tr>

  {/each}

</table>
