<form method="post" action="{{ $design->url('newsletter','admin','browse') }}">
    {{ $designSecurity->inputToken('subscriber_action') }}

    <div class="table-responsive panel panel-default">
        <div class="panel-heading bold">{lang 'Subscribers Manager'}</div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" name="all_action" /></th>
                    <th>{lang 'ID#'}</th>
                    <th>{lang 'Email Address'}</th>
                    <th>{lang 'Name'}</th>
                    <th>{lang 'IP Details'}</th>
                    <th>{lang 'Registration Date'}</th>
                    <th>{lang 'Status'}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th><input type="checkbox" name="all_action" /></th>
                    <th>
                        <button
                            class="btn btn-danger btn-md"
                            type="submit"
                            onclick="return checkChecked()"
                            formaction="{{ $design->url('newsletter','admin','deleteall') }}"
                            >{lang 'Delete'}
                        </button>
                    </th>
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
                        <td><input type="checkbox" name="action[]" value="{% $user->email %}" /></td>
                        <td>{% $user->profileId %}</td>
                        <td>
                            <a href="mailto:{% $user->email %}" title="{lang 'Email the Subscriber'}">
                                {% $user->email %}
                            </a>
                        </td>
                        <td>{% $user->name %}</td>
                        <td>
                            <img src="{{ $design->getSmallFlagIcon(Framework\Geo\Ip\Geo::getCountryCode($user->ip)) }}" title="{lang 'Country Flag'}" alt="{lang 'Country Flag'}" /> {{ $design->ip($user->ip) }}
                        </td>
                        <td>{% $dateTime->get($user->joinDate)->dateTime() %}</td>
                        <td>
                            {if $user->active == SubscriberModel::ACTIVE_STATUS}
                                <span class="green1">{lang 'Active Account'}</span>
                            {else}
                                <span class="red">{lang 'Inactive Account'}</span>
                            {/if}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</form>

{main_include 'page_nav.inc.tpl'}
