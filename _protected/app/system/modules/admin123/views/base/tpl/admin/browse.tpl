<form method="post" action="{{ $design->url(PH7_ADMIN_MOD,'admin','browse') }}">
    {{ $designSecurity->inputToken('admin_action') }}

    <div class="table-responsive panel panel-default">
        <div class="panel-heading bold">{lang 'Admins Manager'}</div>
        <table class="table table-striped">
            <thead>
                <tr>
                  <th><input type="checkbox" name="all_action" /></th>
                  <th>{lang 'Admin ID#'}</th>
                  <th>{lang 'Email Address'}</th>
                  <th>{lang 'User'}</th>
                  <th>{lang 'IP'}</th>
                  <th>{lang 'Join Date'}</th>
                  <th>{lang 'Last Activity'}</th>
                  <th>{lang 'Last Edit'}</th>
                  <th>{lang 'Action'}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                  <th><input type="checkbox" name="all_action" /></th>
                  <th>
                      <button
                          class="btn btn-danger btn-md"
                          type="submit" onclick="return checkChecked()"
                          formaction="{{ $design->url(PH7_ADMIN_MOD,'admin','deleteall') }}"
                          >{lang 'Delete'}
                      </button>
                  </th>
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
                {each $admin in $browse}
                    {{ $adminId = (int)$admin->profileId }}
                    <tr>
                      <td>
                          <input type="checkbox" name="action[]" value="{adminId}_{% $admin->username %}" />
                      </td>
                      <td>{adminId}</td>
                      <td>{% $admin->email %}</td>
                      <td>
                          {% $admin->username %}<br />
                          <span class="gray">{% $admin->firstName %}</span>
                      </td>
                      <td>{{ $design->ip($admin->ip) }}</td>
                      <td class="small">{% $dateTime->get($admin->joinDate)->dateTime() %}</td>
                      <td class="small">
                          {if !empty($admin->lastActivity)}
                              {% $dateTime->get($admin->lastActivity)->dateTime() %}
                          {else}
                              {lang 'No login'}
                          {/if}
                      </td>
                      <td class="small">
                          {if !empty($admin->lastEdit)}
                              {% $dateTime->get($admin->lastEdit)->dateTime() %}
                          {else}
                              {lang 'No editing'}
                          {/if}
                      </td>
                      <td class="small">
                          {if $adminId === $current_admin_id}
                              <a href="{{ $design->url(PH7_ADMIN_MOD, 'account', 'edit') }}" title="{lang 'Edit my Profile'}">{lang 'Edit'}</a>
                          {elseif !AdminCore::isRootProfileId($adminId)}
                              <a href="{{ $design->url(PH7_ADMIN_MOD, 'account', 'edit', $adminId) }}" title="{lang 'Edit this Admin'}">{lang 'Edit'}</a>
                          {/if}

                          {if $adminId !== $current_admin_id AND !AdminCore::isRootProfileId($adminId)}
                              | {{ $design->popupLinkConfirm(t('Delete'), PH7_ADMIN_MOD, 'admin', 'delete', $adminId.'_'.$admin->username) }}
                          {/if}
                      </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</form>

{main_include 'page_nav.inc.tpl'}
