<section class="launch-readiness panel panel-default" aria-labelledby="launch-readiness-heading">
    <div class="panel-heading">
        <h2 id="launch-readiness-heading">{lang 'Launch readiness'}</h2>
    </div>

    <div class="panel-body center">
        <p>{lang 'Resolve these essential technical checks before inviting members or accepting payments.'}</p>

        <progress aria-labelledby="launch-readiness-progress" value="{launch_readiness_completed}" max="{launch_readiness_total}">
            {launch_readiness_completed}/{launch_readiness_total}
        </progress>
        <p id="launch-readiness-progress" class="bold">
            {lang '%0% of %1% essential checks complete.', $launch_readiness_completed, $launch_readiness_total}
        </p>

        <ul class="launch-readiness-list">
            {each $aCheck in $launch_readiness_checks}
                <li class="alert {if $aCheck['is_complete']}alert-success{else}alert-warning{/if}">
                    <strong>
                        {if $aCheck['is_complete']}{lang 'Ready'}{else}{lang 'Action needed'}{/if}:
                    </strong>
                    {% escape($aCheck['label']) %}
                    {if !$aCheck['is_complete']}
                        <a href="{% $str->escapeAttribute($aCheck['action_url']) %}">
                            {% escape($aCheck['action_label']) %}
                        </a>
                    {/if}
                </li>
            {/each}
        </ul>

        <p>
            {lang 'Automated checks cannot verify email delivery, payments, backups, moderation, accessibility, or legal requirements.'}
            <a href="{% $str->escapeAttribute($launch_checklist_url) %}" target="_blank" rel="noopener">
                {lang 'Complete the full launch checklist'}
            </a>.
        </p>
    </div>
</section>
