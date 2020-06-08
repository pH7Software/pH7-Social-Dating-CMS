<form method="post" action="{{ $design->url('affiliate','admin','browse') }}">
    {{ $designSecurity->inputToken('aff_action') }}

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" name="all_action" /></th>
                    <th>{lang 'User ID#'}</th>
                    <th>{lang 'Email Address'}</th>
                    <th>{lang 'User'}</th>
                    <th>{lang 'Refers'}</th>
                    <th>{lang 'Bank Account'}</th>
                    <th>{lang 'Website'}</th>
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
                  <th>
                      <button
                         class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url('affiliate','admin','banall') }}"
                          >{lang 'Ban'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url('affiliate','admin','unbanall') }}"
                          >{lang 'UnBan'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-danger btn-md"
                          type="submit"
                          onclick="return checkChecked()"
                          formaction="{{ $design->url('affiliate','admin','deleteall') }}"
                          >{lang 'Delete'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url('affiliate','admin','approveall') }}"
                          >{lang 'Approve'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url('affiliate','admin','disapproveall') }}"
                          >{lang 'Disapprove'}
                      </button>
                  </th>
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
                    <tr>
                        <td>
                            <input type="checkbox" name="action[]" value="{% $aff->profileId %}_{% $aff->username %}" />
                        </td>
                        <td>{% $aff->profileId %}</td>
                        <td>
                            <a href="mailto:{% $aff->email %}" title="{lang 'Email the Affiliate'}">
                                {% $aff->email %}
                            </a>
                        </td>
                        <td>
                            {% $aff->username %}<br />
                            <span class="small gray">{% $aff->firstName %} {% $aff->lastName %}</span>
                        </td>
                        <td>{% $aff->refer %}</td>
                        <td>{% $aff->bankAccount %}</td>
                        <td>
                            {if !empty($aff->website)}
                                <a href="{% $aff->website %}">{% $aff->website %}</a>
                            {else}
                                {lang 'No website'}
                            {/if}
                        </td>
                        <td>
                            <img src="{{ $design->getSmallFlagIcon(Framework\Geo\Ip\Geo::getCountryCode($aff->ip)) }}" title="{lang 'Country Flag'}" alt="{lang 'Country Flag'}" /> {{ $design->ip($aff->ip) }}
                        </td>
                        <td class="small">{% $dateTime->get($aff->joinDate)->dateTime() %}</td>
                        <td class="small">
                            {if !empty($aff->lastActivity)}
                                {% $dateTime->get($aff->lastActivity)->dateTime() %}
                            {else}
                                {lang 'No login'}
                            {/if}
                        </td>
                        <td class="small">
                            {if !empty($aff->lastEdit)}
                                {% $dateTime->get($aff->lastEdit)->dateTime() %}
                            {else}
                                {lang 'No editing'}
                            {/if}
                        </td>
                        <td class="small">
                            <a href="{{ $design->url('affiliate','account','edit',$aff->profileId) }}" title="{lang "Edit Affiliate's Account"}">{lang 'Edit'}</a> |
                            <a href="{{ $design->url('affiliate','admin','loginuseras',$aff->profileId) }}" title="{lang 'Login as the affiliate (to edit all the user account).'}">{lang 'Login'}</a> |

                            {if $aff->ban == UserCore::BAN_STATUS}
                                {{ $design->popupLinkConfirm(t('Ban'), 'affiliate', 'admin', 'ban', $aff->profileId) }}
                            {else}
                                {{ $design->popupLinkConfirm(t('UnBan'), 'affiliate', 'admin', 'unban', $aff->profileId) }}
                            {/if}

                            {if $aff->active != 1}
                                | {{ $design->popupLinkConfirm(t('Approve'), 'affiliate', 'admin', 'approve', $aff->profileId) }}
                                or {{ $design->popupLinkConfirm(t('Disapprove (notified user by email)'), 'affiliate', 'admin', 'disapprove', $aff->profileId) }}
                            {/if}
                            | {{ $design->popupLinkConfirm(t('Delete'), 'affiliate', 'admin', 'delete', $aff->profileId.'_'.$aff->username) }}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</form>

{main_include 'page_nav.inc.tpl'}
