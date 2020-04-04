<div class="center">
    {if !empty($report)}
        <p>
            <span class="bold">{lang 'Reporter:'}</span> {{ $avatarDesign->get($oUserModel->getUsername($report->reporterId), $oUserModel->getFirstName($report->reporterId) ,null, 64) }}
        </p>
        <p>
            <span class="bold">{lang 'Spammer:'}</span> {{ $avatarDesign->get($oUserModel->getUsername($report->spammerId), $oUserModel->getFirstName($report->spammerId) ,null, 64) }}
        </p>
        <p>
            <span class="bold">{lang 'Contant Type:'}</span> <span class="italic">{% $report->contentType %}</span>
        </p>
        <p>
            <span class="bold">{lang 'URL:'}</span>
            {if !empty($report->url)}
                <span class="italic"><a href="{% $report->url %}" target="_blank">{% $report->url %}</a></span>
            {else}
                <span class="italic underline">{lang 'URL Unavailable'}</span>
            {/if}
        </p>
        <p>
            <span class="bold">{lang 'Description of report'}</span> <span class="italic">{% $report->description %}</span>
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
