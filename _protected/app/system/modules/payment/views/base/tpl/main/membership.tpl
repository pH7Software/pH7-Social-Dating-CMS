<div class="center">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">{lang 'Choose your membership'}</div>
            <div class="panel-body">
                <ul class="list-group">
                    {each $membership in $memberships}
                        {if $membership->enable == 1 AND $membership->price != 0}
                            <li class="list-group-item clearfix">
                                <div class="pull-left">
                                    <h4>{% $membership->name %}</h4>
                                    <h4>{% $membership->price %}</h4>
                                    <p class="italic">{% $membership->description %}</p>
                                </div>
                                <p class="pull-right">
                                    <a class="btn btn-default" href="{{ $design->url('payment', 'main', 'pay', $membership->groupId) }}" title="{lang 'Purchase this membership!'}">{lang 'Choose Plan'}</a>
                                </p>
                            </li>
                        {/if}
                    {/each}
                </ul>
            </div>
        </div>
    </div>
</div>
