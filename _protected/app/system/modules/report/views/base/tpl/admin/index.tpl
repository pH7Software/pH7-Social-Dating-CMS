<div class="center">
    {if empty($reports)}
        <p class="bold">{lang 'No Reports Found'}</p>
    {else}
        <form method="post" action="{{ $design->url('report','admin','inbox') }}">
            {{ $designSecurity->inputToken('report_action') }}
            <ul>
                {each $report in $reports}
                    <li id="report_{% $report->reportId %}">
                        <input type="checkbox" name="action[]" value="{% $report->reportId %}" /> |
                        <a href="{% $design->url('report', 'admin', 'report', $report->reportId) %}">{lang 'View Report'}</a> |
                        {lang 'Reporter:'} {{ $avatarDesign->get($oUserModel->getUsername($report->reporterId), $oUserModel->getFirstName($report->reporterId) ,null, 32) }} |
                        {lang 'Spammer:'} {{ $avatarDesign->get($oUserModel->getUsername($report->spammerId), $oUserModel->getFirstName($report->spammerId) ,null, 32) }}
                        <a class="btn btn-default btn-md" href="javascript:void(0)" onclick="report('delete', {% $report->reportId%},'{csrf_token}')">
                            {lang 'Delete'}
                        </a>
                    </li>
                {/each}
            </ul>

            <p>
                <input type="checkbox" name="all_action" />
                <button
                    class="btn btn-danger btn-md"
                    type="submit"
                    onclick="return checkChecked(false)"
                    formaction="{{ $design->url('report','admin','deleteall') }}"
                    >{lang 'Delete'}
                </button>
            </p>

        </form>
        {main_include 'page_nav.inc.tpl'}
    {/if}
</div>
