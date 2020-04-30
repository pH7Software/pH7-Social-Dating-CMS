{{ $num_enabled_membership = 0 }}

<div class="center">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <h3 class="panel-heading">{lang 'Choose your membership'}</h3>
            <div class="panel-body">
                <ul class="list-group">
                    {each $membership in $memberships}
                        {if $membership->enable == 1 AND $membership->price != 0}
                            {{ $num_enabled_membership++ }}
                            <li class="list-group-item clearfix">
                                <div class="pull-left">
                                    <h4 class="underline">{% $membership->name %}</h4>
                                    <h5>
                                        {% $config->values['module.setting']['currency_sign'] %}{% $membership->price %}
                                        <span class="small">
                                            {if $membership->expirationDays > 0}
                                                {if $membership->expirationDays == 1}
                                                    {lang 'per day', $membership->expirationDays}
                                                {else}
                                                    {lang 'every %0% days', $membership->expirationDays}
                                                {/if}
                                            {else}
                                                <span class="underline">{lang 'one-time payment'}</span>
                                            {/if}
                                        </span>
                                    </h5>
                                    <p class="italic">{% $membership->description %}</p>
                                </div>
                                <p class="pull-right">
                                    <a class="btn btn-default" href="{{ $design->url('payment', 'main', 'pay', $membership->groupId) }}" title="{lang 'Purchase this membership!'}">
                                        {lang 'Choose It'}
                                    </a>
                                </p>
                            </li>
                        {/if}
                    {/each}

                    {if $num_enabled_membership === 0}
                        <li class="red">
                            {lang 'There are no other memberships available for the moment.'}<br />
                            {lang 'Please come back later on 😉'}
                        </li>
                    {/if}
                </ul>
            </div>
        </div>
    </div>
</div>
