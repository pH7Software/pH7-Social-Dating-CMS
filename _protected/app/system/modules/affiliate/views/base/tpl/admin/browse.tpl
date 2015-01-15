<form method="post" action="{{ $design->url('affiliate','admin','browse') }}">
  {{ $designSecurity->inputToken('aff_action') }}

  <div class="panel panel-default">
  <div class="panel-heading bold">{lang 'Affiliates Manager'}</div>
  <table class="table center">

    <thead>
      <tr>
        <th><input type="checkbox" name="all_action" /></th>
        <th>{lang 'User ID#'}</th>
        <th>{lang 'Email Address'}</th>
        <th>{lang 'Username'}</th>
        <th>{lang 'Name'}</th>
        <th>{lang 'Age'}</th>
        <th>{lang 'Refers'}</th>
        <th>{lang 'Bank Account'}</th>
        <th>{lang 'IP'}</th>
        <th>{lang 'Registration Date'}</th>
        <th>{lang 'Last Activity'}</th>
        <th>{lang 'Last Edit'}</th>
        <th>{lang 'Action'}</th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <th><input type="checkbox" name="all_action" /></th>
        <th><button type="submit" formaction="{{ $design->url('affiliate','admin','banall') }}">{lang 'Ban'}</button></th>
        <th><button type="submit" formaction="{{ $design->url('affiliate','admin','unbanall') }}">{lang 'UnBan'}</button></th>
        <th><button type="submit" onclick="return checkChecked()" formaction="{{ $design->url('affiliate','admin','deleteall') }}" class="red">{lang 'Delete'}</button></th>
        <th><button type="submit" formaction="{{ $design->url('affiliate','admin','approveall') }}">{lang 'Approve'}</button></th>
        <th><button type="submit" formaction="{{ $design->url('affiliate','admin','disapproveall') }}">{lang 'Disapprove'}</button></th>
        <th> </th>
        <th> </th>
        <th> </th>
        <th> </th>
        <th> </th>
        <th> </th>
        <th> </th>
      </tr>
    </tfoot>

    <tbody>

      {each $aff in $browse}

        {* Affiliate Age *}
        {{ $aAge = explode('-', $aff->birthDate); $age = (new Framework\Math\Measure\Year($aAge[0], $aAge[1], $aAge[2]))->get() }}

        <tr>
          <td><input type="checkbox" name="action[]" value="{% $aff->profileId %}_{% $aff->username %}" /></td>
          <td>{% $aff->profileId %}</td>
          <td>{% $aff->email %}</td>
          <td>{% $aff->username %}</td>
          <td class="small">{% $aff->lastName %} {% $aff->firstName %}</td>
          <td>{age}</td>
          <td>{% $aff->refer %}</td>
          <td>{% $aff->bankAccount %}</td>
          <td><img src="{{ $design->getSmallFlagIcon( Framework\Geo\Ip\Geo::getCountryCode($aff->ip) ) }}" title="{lang 'IP Country'}" alt="{lang 'IP Country'}" /> {{ $design->ip($aff->ip) }}</td>
          <td>{% $dateTime->get($aff->joinDate)->dateTime() %}</td>
          <td>{if !empty($aff->lastActivity)} {% $dateTime->get($aff->lastActivity)->dateTime() %} {else} {lang 'No last login'} {/if}</td>
          <td>{if !empty($aff->lastEdit)} {% $dateTime->get($aff->lastEdit)->dateTime() %} {else} {lang 'No last editing'} {/if}</td>
          <td class="small">
            <a href="{{ $design->url('affiliate','account','edit',$aff->profileId) }}" title="{lang "Edit Affiliate's Account"}">{lang 'Edit'}</a> |
            <a href="{{ $design->url('affiliate','admin','loginuseras',$aff->profileId) }}" title="{lang 'Login As a member (to all edit this user account).'}">{lang 'Login as User'}</a> |

            {if $aff->ban == 0}
              {{ $design->popupLinkConfirm(t('Ban'), 'affiliate', 'admin', 'ban', $aff->profileId) }}
            {else}
              {{ $design->popupLinkConfirm(t('UnBan'), 'affiliate', 'admin', 'unban', $aff->profileId) }}
            {/if}

            {if $aff->active != 1}
              | {{ $design->popupLinkConfirm(t('Approve'), 'affiliate', 'admin', 'approve', $aff->profileId) }}
              or {{ $design->popupLinkConfirm(t('Disapprove (This ONLY notified user by email).'), 'affiliate', 'admin', 'disapprove', $aff->profileId) }}
            {/if}
            | {{ $design->popupLinkConfirm(t('Delete (Irreversible!)'), 'affiliate', 'admin', 'delete', $aff->profileId.'_'.$aff->username) }}
          </td>

        </tr>

      {/each}

    </tbody>

  </table>
  </div>

</form>

{main_include 'page_nav.inc.tpl'}
