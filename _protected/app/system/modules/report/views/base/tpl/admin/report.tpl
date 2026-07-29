<div class="center">
    {if !empty($report)}
        <p>
            <span class="bold">{lang 'Reporter:'}</span> {{ $avatarDesign->get($oUserModel->getUsername($report->reporterId), $oUserModel->getFirstName($report->reporterId) ,null, 64) }}
        </p>
        <p>
            <span class="bold">{lang 'Spammer:'}</span> {{ $avatarDesign->get($oUserModel->getUsername($report->spammerId), $oUserModel->getFirstName($report->spammerId) ,null, 64) }}
        </p>
        <p>
            <span class="bold">{lang 'Content Type:'}</span> <span class="italic">{% escape($report->contentType) %}</span>
        </p>
        <p>
            <span class="bold">{lang 'URL:'}</span>
            {if !empty($report_url)}
                <span class="italic"><a href="{% $str->escapeAttribute($report_url) %}" target="_blank" rel="noopener noreferrer nofollow">{% escape($report_url) %}</a></span>
            {else}
                <span class="italic underline">{lang 'URL Unavailable'}</span>
            {/if}
        </p>
        <p>
            <span class="bold">{lang 'Description of report'}</span> <span class="italic">{% nl2br(escape($report->description)) %}</span>
        </p>
        <p>
            <span class="bold">{lang 'Date:'}</span><span class="italic">{% $dateTime->get($report->dateTime)->dateTime() %}</span>
        </p>
        <p>&nbsp;</p>
        <div class="btn btn-default btn-md inline">
            {{ LinkCoreForm::display(t('Delete Report'), 'report', 'admin', 'delete', array('id' => $report->reportId)) }}
        </div>
    {else}
        <p class="err_msg">{lang 'Oops! Report not found.'}</p>
    {/if}
</div>
