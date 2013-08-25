<form method="post" action="{{ $design->url(PH7_ADMIN_MOD,'user','browse') }}">
{{ $designSecurity->inputToken('admin_action') }}

<table class="center">

<thead>
<tr>
<th><input type="checkbox" name="all_action" /></th>
<th>{lang 'Admin ID#'}</th>
<th>{lang 'Email Address'}</th>
<th>{lang 'Username'}</th>
<th>{lang 'First Name'}</th>
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
<th><button type="submit" onclick="return checkChecked()" formaction="{{ $design->url(PH7_ADMIN_MOD,'admin','deleteall') }}" class="red">{lang 'Delete'}</button></th>
<th> </th>
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

<tr>
<td><input type="checkbox" name="action[]" value="{% $admin->profileId %}_{% $admin->username %}" /></td>
<td>{% $admin->profileId %}</td>
<td>{% $admin->email %}</td>
<td>{% $admin->username %}</td>
<td>{{ if(!empty($admin->name)) echo $admin->name }} &nbsp; {% $admin->firstName %}</td>
<td><a href="{% Framework\Ip\Ip::api($admin->ip) %}" title="{lang 'See information from this user IP'}" target="_blank">{% $admin->ip %}</a></td>
<td>{% $dateTime->get($admin->joinDate)->dateTime() %}</td>
<td>{if !empty($admin->lastActivity)} {% $dateTime->get($admin->lastActivity)->dateTime() %} {else} {lang 'No last login'} {/if}</td>
<td>{if !empty($admin->lastEdit)} {% $dateTime->get($admin->lastEdit)->dateTime() %} {else} {lang 'No last editing'} {/if}</td>
<td class="small"><a href="{{ $design->url(PH7_ADMIN_MOD,'account','edit',$admin->profileId) }}" title="{lang 'Edit this Admin'}">{lang 'Edit'}</a> |
{{ $design->popupLinkConfirm(t('Delete (Irreversible!)'), PH7_ADMIN_MOD, 'admin', 'delete', $admin->profileId.'_'.$admin->username) }}</td>
</tr>

{/each}
</tbody>

</table>

</form>

{main_include 'page_nav.inc.tpl'}
