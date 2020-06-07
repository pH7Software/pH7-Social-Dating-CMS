<form method="post" action="{{ $design->url(PH7_ADMIN_MOD, 'user', 'browse') }}">
    {{ $designSecurity->inputToken('user_action') }}

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" name="all_action" /></th>
                    <th>{lang 'User ID#'}</th>
                    <th>{lang 'Email Address'}</th>
                    <th>{lang 'User'}</th>
                    <th>{lang 'Profile Photo'}</th>
                    <th>{lang 'IP'}</th>
                    <th>{lang 'Membership Group + ID'}</th>
                    <th>{lang 'Registration Date'}</th>
                    <th>{lang 'Last Activity'}</th>
                    <th>{lang 'Last Edit'}</th>
                    <th>{lang 'Reference'}</th>
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
                          formaction="{{ $design->url(PH7_ADMIN_MOD, 'user', 'banall') }}"
                          >{lang 'Ban'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url(PH7_ADMIN_MOD, 'user', 'unbanall') }}"
                          >{lang 'UnBan'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-danger btn-md"
                          type="submit"
                          onclick="return checkChecked()"
                          formaction="{{ $design->url(PH7_ADMIN_MOD, 'user', 'deleteall') }}"
                          >{lang 'Delete'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url(PH7_ADMIN_MOD, 'user', 'approveall') }}"
                          >{lang 'Approve'}
                      </button>
                  </th>
                  <th>
                      <button
                          class="btn btn-default btn-md"
                          type="submit"
                          formaction="{{ $design->url(PH7_ADMIN_MOD, 'user', 'disapproveall') }}"
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
                {each $user in $browse}
                    <tr>
                        <td>
                            <input type="checkbox" name="action[]" value="{% $user->profileId %}_{% $user->username %}" />
                        </td>
                        <td>{% $user->profileId %}</td>
                        <td>
                            <a href="mailto:{% $user->email %}" title="{lang 'Email the User'}">
                                {% $user->email %}
                            </a>
                        </td>
                        <td>
                            {{ $design->getProfileLink($user->username) }}<br />
                            <span class="gray">{% $user->firstName %}</span>
                        </td>
                        <td>{{ $avatarDesign->get($user->username, $user->firstName, null, 32) }}</td>
                        <td>
                            <img src="{{ $design->getSmallFlagIcon(Framework\Geo\Ip\Geo::getCountryCode($user->ip)) }}" title="{lang 'Country Flag'}" alt="{lang 'Country Flag'}" /> {{ $design->ip($user->ip) }}
                        </td>
                        <td>{% $user->membershipName %} ({% $user->groupId %})</td> {* Name of the Membership Group *}
                        <td class="small">{% $dateTime->get($user->joinDate)->dateTime() %}</td>
                        <td class="small">
                            {if !empty($user->lastActivity)}
                                {% $dateTime->get($user->lastActivity)->dateTime() %}
                            {else}
                                {lang 'No login'}
                            {/if}
                        </td>
                        <td class="small">
                            {if !empty($user->lastEdit)}
                                {% $dateTime->get($user->lastEdit)->dateTime() %}
                            {else}
                                {lang 'No editing'}
                            {/if}
                        </td>
                        <td class="small">{% $user->reference %}</td>
                        <td class="small">
                            <a href="{{ $design->url('user', 'setting', 'edit', $user->profileId) }}" title="{lang "Edit User's Profile Information"}">{lang 'Edit'}</a> •
                            <a href="{{ $design->url('user', 'setting', 'avatar', "$user->profileId,$user->username,$user->firstName,$user->sex", false) }}" title="{lang "Edit User's Profile Photo"}">{lang 'Profile Photo'}</a> •
                            <a href="{{ $design->url('user','setting','design', "$user->profileId,$user->username,$user->firstName,$user->sex", false) }}" title="{lang "Edit the Wallpaper of the User's Profile Page"}">{lang 'Wallpaper'}</a> •
                            <a href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'password') }}/{% $user->email %}" title="{lang "Edit the User's Password"}">{lang 'Password'}</a>
                            {if $is_mail_enabled}
                                • <a href="{{ $design->url('mail', 'main', 'compose', $user->username) }}" title="{lang 'Send a message to this user'}">{lang 'Send PM'}</a>
                            {/if}
                            • <a href="{{ $design->url(PH7_ADMIN_MOD, 'user', 'loginuseras', $user->profileId) }}" title="{lang 'Login as the user (to edit all the user account).'}">{lang 'Login'}</a> •

                            {if $user->ban == UserCore::BAN_STATUS}
                                {{ $design->popupLinkConfirm(t('Ban'), PH7_ADMIN_MOD, 'user', 'ban', $user->profileId) }}
                            {else}
                                {{ $design->popupLinkConfirm(t('UnBan'), PH7_ADMIN_MOD, 'user', 'unban', $user->profileId) }}
                            {/if}

                            {if $user->active != 1}
                                • {{ $design->popupLinkConfirm(t('Approve'), PH7_ADMIN_MOD, 'user', 'approve', $user->profileId) }}
                                or {{ $design->popupLinkConfirm(t('Disapprove (notified user by email)'), PH7_ADMIN_MOD, 'user', 'disapprove', $user->profileId) }}
                            {/if}

                            • {{ $design->popupLinkConfirm(t('Delete'), PH7_ADMIN_MOD, 'user', 'delete', $user->profileId.'_'.$user->username) }}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>

        {if $total_users > UserMilestoneCore::MILLENARIAN_WEBSITE}
            {manual_include 'milestone_reached.inc.tpl'}
        {/if}
    </div>
</form>

{main_include 'page_nav.inc.tpl'}
