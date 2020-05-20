<table class="center">
    <tr>
        <th>{lang 'Group ID#'}</th>
        <th>{lang 'Name'}</th>
        <th>{lang 'Price (%0%)', $config->values['module.setting']['currency_code']}</th>
        <th>{lang 'Expiration'}</th>
        <th>{lang 'Active'}</th>
        <th>{lang 'Action'}</th>
    </tr>

    {each $membership in $memberships}
        <tr>
            <td>{% $membership->groupId %}</td>
            <td>{% $membership->name %}</td>
            <td>{% $membership->price %}</td>
            <td>
                {if $membership->expirationDays == 0}
                    {lang 'Never'}
                {else}
                    {% $membership->expirationDays %}
                {/if}
            </td>
            <td>{if $membership->enable == 1} <span class="green1">{lang 'Enable'}</span> {else} <span class="red">{lang 'Disable'}</span> {/if}</td>
            <td>
                <a href="{{ $design->url('payment', 'admin', 'editmembership', $membership->groupId) }}">{lang 'Edit'}</a> |
                {if !GroupId::undeletable($membership->groupId)}
                    {{ $design->popupLinkConfirm(t('Delete (Irreversible!)'), 'payment', 'admin', 'deletemembership', $membership->groupId) }}
                {else}
                    <span class="gray">{lang 'Not deletable'}</span>
                {/if}
            </td>
        </tr>
    {/each}
</table>

<div class="s_tMarg center">
    <hr />
    <p>
        <a class="btn btn-default btn-sm"  href="{{ $design->url('payment', 'admin', 'addmembership') }}">
            {lang 'Add new Membership'}
        </a>
    </p>
</div>
