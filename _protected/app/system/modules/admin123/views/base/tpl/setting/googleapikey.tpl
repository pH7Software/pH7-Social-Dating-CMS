<div class="col-md-8">
    {{ ConfigFileCoreForm::display('service.api', PH7_PATH_APP_CONFIG) }}

    <p class="small">{lang}Google Map might not be working if you are not using a API key. It is highly recommended to use one.{/lang}<br />
    {lang 'You can get one <a href="%0%">here</a>. Enable Google Map API (which is disabled by default). Finally create an API key. Select "Api Key" for the Credential type, then select "Server Key", them you will get your API key to paste in this form. ', 'http://console.developers.google.com/apis/library'}</p>
</div>